<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;
use Hashids\Hashids;

/*----------------------------------------------------------
    Modul Name  : Database Member
    Desc        : Menyimpan data member, proses member
    Sub fungsi  : 
        - getby_id          : Mendapatkan data user dari username
        - change_password   : Ubah password
------------------------------------------------------------*/


class Mdl_member extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'member';
    protected $primaryKey = 'id';

    protected $allowedFields = ['email', 'passwd', 'id_referral', 'timezone', 'otp', 'role', 'status', 'ip_addr', 'is_delete'];

    protected $returnType = 'array';
    protected $useTimestamps = true;

    // public function __construct()
    // {
    //     $this->db = \Config\Database::connect();
    // }
    public function get_all()
    {

        try {

            $sql = "SELECT
                        m.role,
                        m.id,
                        m.email,
                        m.refcode,
                        m.created_at,
                        m.status,
                        -- fund
                        COALESCE((
                            SELECT SUM(amount)
                            FROM member_deposit
                            WHERE status = 'complete' AND member_id = m.id
                        ), 0)
                        +
                        COALESCE((
                            SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id = m.id AND (jenis = 'balance' or jenis='comission') AND withdraw_type = 'usdt'
                        ), 0)
                        -
                        COALESCE((
                            SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id = m.id
                            AND (
                                (jenis = 'withdraw' AND status <> 'rejected' AND (withdraw_type = 'usdt' or withdraw_type = 'usdc'))
                                OR (jenis = 'trade' AND withdraw_type = 'usdt')
                            )
                        ), 0) AS fund,

                        -- trade
                        FLOOR((
                            COALESCE((
                                SELECT -SUM(master_wallet)
                                FROM wallet
                                WHERE member_id = m.id
                            ), 0)
                            - COALESCE((
                                SELECT SUM(
                                    CASE
                                        WHEN s.type LIKE 'Buy%' THEN ms.amount_usdt
                                    END
                                )
                                FROM member_sinyal ms
                                JOIN sinyal s ON s.id = ms.sinyal_id
                                WHERE ms.member_id = m.id
                                AND s.status != 'canceled'
                            ), 0)
                            + COALESCE((
                                SELECT SUM(
                                    CASE
                                        WHEN s.type LIKE 'Sell%' THEN ms.amount_usdt
                                    END
                                )
                                FROM member_sinyal ms
                                JOIN sinyal s ON s.id = ms.sinyal_id
                                WHERE ms.member_id = m.id
                                AND s.status = 'filled'
                            ), 0)
                            + COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id
                                AND jenis = 'trade'
                            ), 0)
                            - COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id
                                AND jenis = 'balance'
                                AND withdraw_type = 'usdt'
                            ), 0)
                        ) * 100) / 100 AS trade,

                        -- Jumlah referral aktif
                        COALESCE(COUNT(r.id), 0) AS referral

                    FROM member m

                    LEFT JOIN member r
                        ON r.id_referral = m.id
                         AND r.status IN ('active', 'referral')
                        AND r.is_delete = FALSE

                    WHERE
                        m.is_delete = FALSE

                    GROUP BY
                        m.role,
                        m.id,
                        m.email,
                        m.refcode,
                        m.created_at,
                        m.status";

            $query = $this->db->query($sql)->getResult();

            if (!$query) {
                return (object) [
                    'code'    => 404,
                    'message' => []
                ];
            }
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred'
            ];
        }

        return (object) [
            "code"    => 200,
            "message"    => $query
        ];
    }

    public function get_admin()
    {

        try {

            $sql = "SELECT
                        m.email,
                        mr.alias,
                        mr.access
                    FROM
                        member m
                        LEFT JOIN member_role mr ON mr.member_id = m.id
                    WHERE
                        m.role NOT IN ('member', 'superadmin')
                        AND m.status = 'active'
                        AND m.is_delete = false";

            $query = $this->db->query($sql)->getResult();

            if (!$query) {
                return (object) [
                    'code'    => 200,
                    'message' => []
                ];
            }
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred'
            ];
        }

        return (object) [
            "code"    => 200,
            "message"    => $query
        ];
    }

    public function getby_email($email)
    {
        $sql = "SELECT * FROM member
                WHERE
                    email = ?
                LIMIT
                    1";

        $query = $this->db->query($sql, [$email])->getRow();
        if (!$query) {
            return (object) [
                "code"    => "404",
                "message" => "User not found"
            ];
        }

        if (in_array($query->status, ['disabled', 'new'], true)) {
            return (object) [
                "code"    => "400",
                "message" => "Your account has not been activated"
            ];
        }

        return (object) [
            "code"    => "200",
            "message" => $query
        ];
    }


    // Tambahkan data ke database
    public function add($data)
    {
        try {
            $member = $this->db->table("member");
            $member->insert($data);
            $id     = $this->db->insertID();

            if (!$data['refcode'] && $data['role'] == 'referral') {
                $mdata = array(
                    "refcode"   => substr($this->generate_token($id), 0, 8),
                );
                $member->where("id", $id);
                $member->update($mdata);
            }

            return (object) [
                'success'  => true,
                'message' => 'User registered successfully'
            ];
        } catch (\Exception $e) {
            return (object) [
                'success'  => false,
                'code'    => $e->getCode(),
                'message' => 'An error occurred.' . $e
            ];
        }
    }


    private function generate_token($id)
    {
        $hashids = new Hashids('', 8, 'abcdefghijklmnopqrstuvwxyz1234567890');
        return $hashids->encode((int)$id, time(), rand());
    }

    public function getby_refcode($refcode)
    {
        $query = $this->select('id')->where('refcode', $refcode)->first();

        if (!$query) {
            return (object) [
                'exist' => false,
                'message' => 'Referral code not found'
            ];
        }

        return (object) [
            'exist' => true,
            'message' => 'Referral found',
            'id'    => $query['id']
        ];
    }

    public function getby_ids($ids)
    {
        $query = $this->select('*')->whereIn('id', $ids)->findAll();

        if (!$query) {
            return (object) [
                'code'    => 404,
                'message' => 'Member not found'
            ];
        }

        return (object) [
            'code' => 200,
            'message' => $query,
        ];
    }

    public function check_upline($id_member)
    {
        $query = $this->select('id_referral')->where('id', $id_member)->first();

        if (!$query) {
            return (object) [
                'code' => 400,
                'message' => false
            ];
        }

        return (object) [
            'code' => 200,
            'message' => $query['id_referral'],
        ];
    }

    public function update_otp($mdata)
    {
        try {
            // Cek apakah email ada di database
            $member = $this->where('email', $mdata['email'])->first();

            if (!$member) {
                return (object) [
                    'code'    => 404,
                    'message' => 'User not found'
                ];
            }

            // Update OTP berdasarkan email
            $this->set('otp', $mdata['otp'])->where('email', $mdata['email'])->update();

            return (object) [
                'code'    => 200,
                'message' => 'Your token has been resent via email'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while updating token'
            ];
        }
    }

    public function activate($mdata)
    {
        try {
            // Cari member berdasarkan email dan otp
            $valid = $this->where('email', $mdata['email'])
                ->where('otp', $mdata['otp'])
                ->first();

            if (!$valid) {
                return (object) [
                    'code'    => 400,
                    'message' => 'Invalid token'
                ];
            }

            // Update status menjadi "member"
            $this->set(['status' => 'active', 'otp' => null])
                ->where('email', $mdata['email'])
                ->update();

            return (object) [
                'code'    => 200,
                'message' => 'Your account has been activated'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while activating the account'
            ];
        }
    }


    public function reset_password($mdata, $isgodmode)
    {
        try {
            // Validasi OTP dan email
            $builder = $this->where('email', $mdata['email']);

            if (!$isgodmode) {
                $builder = $builder->where('otp', $mdata['otp']);
            }

            $valid = $builder->first();

            if (!$valid) {
                return (object) [
                    'code'    => 400,
                    'message' => 'Invalid token'
                ];
            }

            // Update password dan hapus OTP
            $this->set([
                'status' => $valid['status'] == 'new' ? 'active' : $valid['status'],
                'passwd' => $mdata['password'],
                'otp'    => null // menghapus otp
            ])
                ->where('email', $mdata['email'])
                ->update();

            return (object) [
                'code'    => 200,
                'message' => 'Password has been reset successfully'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while resetting the password'
            ];
        }
    }

    public function deleteby_email($mdata)
    {
        try {
            $sql = "SELECT email, role FROM member where email = ?";
            $user = $this->db->query($sql, $mdata['email'])->getRow();

            if (!$user) {
                return (object) [
                    'code'    => 404,
                    'message' => 'User not found.'
                ];
            }

            if ($user->role == 'superadmin') {
                return (object) [
                    'code'    => 403,
                    'message' => 'Action denied. Superadmin cannot be deleted.'
                ];
            }

            $this->set([
                'email' => $mdata['new_email'],
                'is_delete' => true
            ])->where('email', $user->email)->update();
        } catch (Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while deleting the account.'
            ];
        }

        return (object) [
            'code'    => 201,
            'message' => 'Account has been successfully deleted.'
        ];
    }

    public function getStatistics()
    {
        try {
            $sql = "SELECT
                    (
                        SELECT COALESCE(COUNT(DISTINCT m.id), 0)
                        FROM member m
                        WHERE m.status != 'disabled' AND m.is_delete = FALSE AND m.role!='superadmin'
                    ) AS members,
                    
                    (
                        SELECT COALESCE(COUNT(DISTINCT s.id), 0)
                        FROM sinyal s
                    ) AS signals,
                    
                    (
                        SELECT COALESCE(COUNT(DISTINCT m.id), 0)
                        FROM member m
                       WHERE m.status IN ('active', 'referral') AND m.is_delete = FALSE AND m.role != 'superadmin'
                    ) AS active_members,
                    
                    (
                        SELECT COALESCE(COUNT(DISTINCT m.id), 0)
                        FROM member m
                        WHERE m.role = 'referral' AND m.is_delete = FALSE
                    ) AS referrals";

            $result = $this->db->query($sql)->getRow();

            return (object) [
                'code'    => 200,
                'message' => 'Membership statistics retrieved successfully.',
                'data'    => $result
            ];
        } catch (\Throwable $th) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while retrieving membership statistics. Please try again later.'
            ];
        }
    }

    public function set_status($mdata)
    {
        try {

            // Update status "member"
            $this->set(['status' => $mdata['status']])
                ->where('email', $mdata['email'])
                ->update();

            return (object) [
                'code'    => 200,
                'message' => 'The account has been updated.'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred'
            ];
        }
    }

    public function detail_member_byEmail($email)
    {
        try {
            $sql = "SELECT
                        m.id,
                        m.created_at AS start_date,
                        CASE 
                            WHEN m.role = 'member' THEN 'Normal Member'
                            WHEN m.role = 'referral' THEN 'Referral Member'
                            ELSE 'Unknown'
                        END AS membership_status,
                        '-' AS subscription_plan,
                        '-' AS subscription_status,
                        m.refcode,
                        m.role,
                        -- USDT balance: deposits + balance withdraws - real withdraws/trades
                        COALESCE((
                            SELECT SUM(amount)
                            FROM member_deposit
                            WHERE status = 'complete' AND member_id = m.id
                        ), 0)
                        +
                        COALESCE((
                            SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id = m.id AND (jenis = 'balance' or jenis='comission') AND withdraw_type = 'usdt'
                        ), 0)
                        -
                        COALESCE((
                            SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id = m.id
                            AND (
                                (jenis = 'withdraw' AND status <> 'rejected' AND (withdraw_type = 'usdt' or withdraw_type = 'usdc'))
                                OR (jenis = 'trade' AND withdraw_type = 'usdt')
                            )
                        ), 0) AS fund_usdt,
                        
                        -- BTC balance: balance - trade - actual withdrawn
                        COALESCE((
                            SELECT SUM(x.amount)
                            FROM withdraw x
                            WHERE x.member_id = m.id
                            AND x.jenis = 'balance'
                            AND x.withdraw_type = 'btc'
                        ), 0)
                        -
                        COALESCE((
                            SELECT SUM(y.amount)
                            FROM withdraw y
                            WHERE y.member_id = m.id
                            AND y.jenis = 'trade'
                            AND y.withdraw_type = 'btc'
                        ), 0)
                        -
                        COALESCE((
                            SELECT SUM(z.amount)
                            FROM withdraw z
                            WHERE z.member_id = m.id
                            AND (
                                (z.jenis = 'withdraw' AND z.status <> 'rejected' AND z.withdraw_type = 'btc')
                                OR (z.jenis = 'trade' AND z.withdraw_type = 'btc')
                            )
                        ), 0) AS fund_btc,
                        FLOOR((
                            COALESCE((
                                SELECT -SUM(master_wallet)
                                FROM wallet
                                WHERE member_id = m.id
                            ), 0)
                            - COALESCE((
                                SELECT SUM(
                                    CASE
                                        WHEN s.type LIKE 'Buy%' THEN ms.amount_usdt
                                    END
                                )
                                FROM member_sinyal ms
                                JOIN sinyal s ON s.id = ms.sinyal_id
                                WHERE ms.member_id = m.id
                                AND s.status != 'canceled'
                            ), 0)
                            + COALESCE((
                                SELECT SUM(
                                    CASE
                                        WHEN s.type LIKE 'Sell%' THEN ms.amount_usdt
                                    END
                                )
                                FROM member_sinyal ms
                                JOIN sinyal s ON s.id = ms.sinyal_id
                                WHERE ms.member_id = m.id
                                AND s.status = 'filled'
                            ), 0)
                            + COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id
                                AND jenis = 'trade'
                            ), 0)
                            - COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id
                                AND jenis = 'balance'
                                AND withdraw_type = 'usdt'
                            ), 0)
                        ) * 100) / 100 AS trade_usdt,
                      COALESCE(
                        (SELECT SUM(
                           CASE
                             WHEN s.type LIKE 'Buy%'  THEN ms.amount_btc
                             WHEN s.type LIKE 'Sell%' THEN -ms.amount_btc
                             ELSE 0
                           END
                         )
                         FROM member_sinyal ms
                         JOIN sinyal s  ON s.id = ms.sinyal_id
                         WHERE ms.member_id = m.id
                         AND s.status='filled'
                        ), 0
                      )
                      + COALESCE(
                        (SELECT SUM(x.amount)
                         FROM withdraw x
                         WHERE x.member_id     = m.id
                           AND x.jenis         = 'trade'
                           AND x.withdraw_type = 'btc'
                        ), 0
                      )
                      - COALESCE(
                        (SELECT SUM(y.amount)
                         FROM withdraw y
                         WHERE y.member_id     = m.id
                           AND y.jenis         = 'balance'
                           AND y.withdraw_type = 'btc'
                        ), 0
                      )
                      AS trade_btc
                    FROM
                        member m
                    WHERE
                        m.email = ?";

            $query = $this->db->query($sql, [$email])->getRow();

            if (!$query) {
                return (object) [
                    'code'    => 404,
                    'message' => 'No member found with the given email address.'
                ];
            }
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An unexpected error occurred. Please try again later.'
            ];
        }

        return (object) [
            'code'    => 200,
            'message' => 'An unexpected error occurred. Please try again later.',
            'data'    => $query
        ];
    }

    public function get_downline_byId($id_member)
    {
        try {
            $sql = "SELECT
                        *,
                    '-' as subscription
                    FROM
                        member
                    WHERE
                        id_referral = ?
                        AND is_delete = 0
                        AND status IN ('active', 'referral')";
            $query = $this->db->query($sql, [$id_member])->getResult();

            if (!$query) {
                return (object) [
                    'code' => 200,
                    'message' => 'No active downline members found.',
                    'data'  => []
                ];
            }
        } catch (\Throwable $th) {
            return (object) [
                'code' => 500,
                'message' => 'An unexpected error occurred. Please try again later.'
            ];
        }

        return (object) [
            'code' => 200,
            'message' => 'Downline members retrieved successfully..',
            'data'    => $query
        ];
    }


    public function get_referral_member()
    {
        try {
            $sql = "SELECT
                        m.id,
                        m.email,
                        m.refcode,
                        0 as commission,
                        COALESCE(COUNT(r.id), 0) AS referral,
                        'hedgefund' as product
                    FROM
                        member m
                        LEFT JOIN member r ON r.id_referral = m.id
                        AND r.status IN ('active', 'referral')
                        AND r.is_delete = FALSE
                    WHERE
                        m.is_delete = FALSE
                        AND m.role = 'referral'
                        -- AND m.status = 'referral'
                    GROUP BY
                        m.role, m.id, m.email,
                        m.refcode, m.created_at, m.status";
            $query = $this->db->query($sql)->getResult();

            if (!$query) {
                return (object) [
                    'code' => 200,
                    'message' => [],
                ];
            }
        } catch (\Throwable $th) {
            return (object) [
                'code' => 500,
                'message' => 'An unexpected error occurred. Please try again later.'
            ];
        }

        return (object) [
            'code' => 200,
            'message' => 'Downline members retrieved successfully..',
            'data'    => $query
        ];
    }


    public function otp_check($mdata)
    {
        try {
            // Validasi OTP dan email
            $valid = $this->where('email', $mdata['email'])
                ->where('otp', $mdata['otp'])
                ->first();

            if (!$valid) {
                return (object) [
                    'code'    => 400,
                    'message' => false
                ];
            }

            return (object) [
                'code'    => 200,
                'message' => true
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => false
            ];
        }
    }

    public function get_activemember()
    {
        try {

            $sql = "SELECT
                        m.role,
                        m.id,
                        m.email,
                        m.refcode,
                        m.created_at,
                        m.status,

                        -- Total capital member
                        (
                        -- from trade balance
                            COALESCE((
                                SELECT SUM(client_wallet)
                                FROM wallet w
                                WHERE w.member_id = m.id
                            ), 0)
                            +
                            COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw w
                                WHERE w.member_id = m.id AND w.jenis = 'trade'
                            ), 0)
                            +
                            COALESCE((
                                SELECT SUM(amount)
                                FROM member_deposit d
                                WHERE d.member_id = m.id AND d.status = 'complete'
                            ), 0)

                            -- from fund balance
                            +
                            COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw w
                                WHERE w.member_id = m.id AND w.jenis = 'balance'
                            ), 0)
                            -
                            COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw w
                                WHERE w.member_id = m.id AND w.jenis = 'withdraw'
                            ), 0)
                        ) AS initial_capital,

                        -- Jumlah referral aktif
                        COALESCE(COUNT(r.id), 0) AS referral

                    FROM member m

                    LEFT JOIN member r
                        ON r.id_referral = m.id
                         AND r.status IN ('active', 'referral')
                        AND r.is_delete = FALSE

                    WHERE
                        m.is_delete = FALSE
                        AND m.role = 'member'
                        AND m.status IN ('active','referral')
                    GROUP BY
                        m.role,
                        m.id,
                        m.email,
                        m.refcode,
                        m.created_at,
                        m.status";

            $query = $this->db->query($sql)->getResult();

            if (!$query) {
                return (object) [
                    'code'    => 404,
                    'message' => []
                ];
            }
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred'
            ];
        }

        return (object) [
            "code"    => 200,
            "message"    => $query
        ];
    }

    public function history_trade($id_member)
    {
        try {
            $sql = "-- order filled
                        SELECT
                            s.status,
                            s.entry_price,
                            s.created_at AS date,
                            CASE
                                WHEN s.status = 'pending' THEN 0
                                ELSE ms.amount_btc
                            END AS amount_btc,
                            ms.amount_usdt,
                            SUBSTRING_INDEX(s.type, ' ', 1) AS position
                        FROM
                            sinyal s
                            INNER JOIN member_sinyal ms ON ms.sinyal_id = s.id
                        WHERE
                            ms.member_id = ?

                        UNION ALL

                        -- order pending/canceled
                        SELECT
                            s.status,
                            s.entry_price,
                            s.created_at AS date,
                            CASE 
                                WHEN SUBSTRING_INDEX(s.type, ' ', 1) = 'Buy' THEN 0 
                                ELSE ms.amount_btc 
                            END AS amount_btc,
                            CASE 
                                WHEN SUBSTRING_INDEX(s.type, ' ', 1) = 'Sell' THEN 0 
                                ELSE ms.amount_usdt 
                            END AS amount_usdt,
                            SUBSTRING_INDEX(s.type, ' ', 1) AS position
                        FROM
                            sinyal s
                            INNER JOIN member_sinyal ms ON ms.sinyal_id = s.pair_id
                        WHERE
                            ms.member_id = ?
                            AND s.status = 'pending'";
            $query = $this->db->query($sql, [$id_member, $id_member])->getResult();

            if (!$query) {
                return (object) [
                    'code' => 200,
                    'message' => []
                ];
            }
        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred.'
            ];
        }

        return (object) [
            'code' => 200,
            'message' => $query
        ];
    }

    public function getTotal_balance() {
        try {

            $sql = "SELECT
                        ROUND(SUM(fund_balance), 2) AS fund_usdt,
                        ROUND(SUM(trade_balance), 2) AS trade_usdt,
                        0 as fund_btc,
                        ROUND(SUM(trade_btc), 6) AS trade_btc
                    FROM (
                        SELECT
                            m.id AS member_id,
                                -- USDT balance: deposits + balance withdraws - real withdraws/trades
                                COALESCE(
                            (
                                        SELECT
                                            SUM(amount)
                                        FROM
                                            member_deposit
                                        WHERE
                                            status = 'complete'
                                            AND member_id = m.id
                                    ),
                                    0
                                ) + COALESCE(
                            (
                                        SELECT
                                            SUM(amount)
                                        FROM
                                            withdraw
                                        WHERE
                                            member_id = m.id
                                            AND (
                                                jenis = 'balance'
                                                or jenis = 'comission'
                                            )
                                            AND withdraw_type = 'usdt'
                                    ),
                                    0
                                ) - COALESCE(
                            (
                                        SELECT
                                            SUM(amount)
                                        FROM
                                            withdraw
                                        WHERE
                                            member_id = m.id
                                            AND (
                                                (
                                                    jenis = 'withdraw'
                                                    AND status <> 'rejected'
                                                    AND (
                                                        withdraw_type = 'usdt'
                                                        or withdraw_type = 'usdc'
                                                    )
                                                )
                                                OR (
                                                    jenis = 'trade'
                                                    AND withdraw_type = 'usdt'
                                                )
                                            )
                                    ),
                                    0
                                ) AS fund_balance,
                            FLOOR((
                                COALESCE((
                                    SELECT -SUM(master_wallet)
                                    FROM wallet
                                    WHERE member_id = m.id
                                ), 0)
                                - COALESCE((
                                    SELECT SUM(CASE WHEN s.type LIKE 'Buy%' THEN ms.amount_usdt END)
                                    FROM member_sinyal ms
                                    JOIN sinyal s ON s.id = ms.sinyal_id
                                    WHERE ms.member_id = m.id AND s.status != 'canceled'
                                ), 0)
                                + COALESCE((
                                    SELECT SUM(CASE WHEN s.type LIKE 'Sell%' THEN ms.amount_usdt END)
                                    FROM member_sinyal ms
                                    JOIN sinyal s ON s.id = ms.sinyal_id
                                    WHERE ms.member_id = m.id AND s.status = 'filled'
                                ), 0)
                                + COALESCE((
                                    SELECT SUM(amount)
                                    FROM withdraw
                                    WHERE member_id = m.id AND jenis = 'trade'
                                ), 0)
                                - COALESCE((
                                    SELECT SUM(amount)
                                    FROM withdraw
                                    WHERE member_id = m.id AND jenis = 'balance' AND withdraw_type = 'usdt'
                                ), 0)
                            ) * 100) / 100 AS trade_balance,
                            -- BTC trade
                            COALESCE(
                        (SELECT SUM(
                           CASE
                             WHEN s.type LIKE 'Buy%'  THEN ms.amount_btc
                             WHEN s.type LIKE 'Sell%' THEN -ms.amount_btc
                             ELSE 0
                           END
                         )
                         FROM member_sinyal ms
                         JOIN sinyal s  ON s.id = ms.sinyal_id
                         WHERE ms.member_id = m.id
                         AND s.status='filled'
                        ), 0
                      )
                      + COALESCE(
                        (SELECT SUM(x.amount)
                         FROM withdraw x
                         WHERE x.member_id     = m.id
                           AND x.jenis         = 'trade'
                           AND x.withdraw_type = 'btc'
                        ), 0
                      )
                      - COALESCE(
                        (SELECT SUM(y.amount)
                         FROM withdraw y
                         WHERE y.member_id     = m.id
                           AND y.jenis         = 'balance'
                           AND y.withdraw_type = 'btc'
                        ), 0
                      )
                      AS trade_btc
                        FROM
                            member m
                    ) AS subquery";
            $query = $this->db->query($sql)->getRow();

            return (object) [
                'code' => 200,
                'message' => $query
            ];

        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred.' .$e
            ];
        }
    }
}
