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

    protected $allowedFields = ['email', 'passwd', 'id_referral', 'timezone', 'otp', 'role', 'status', 'ip_addr', 'is_delete', 'phone_number'];

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
                        m.phone_number,
                        m.created_at,
                        m.status,
                        CASE 
                            WHEN EXISTS (
                                SELECT 1 
                                FROM member_deposit md
                                WHERE md.member_id = m.id AND md.status = 'complete'
                            )
                            OR EXISTS (
                                SELECT 1
                                FROM withdraw w
                                WHERE w.member_id = m.id 
                                  AND (w.jenis = 'balance' OR w.jenis = 'comission') 
                                  AND w.withdraw_type = 'usdt'
                            )
                            THEN 1
                            ELSE 0
                        END AS has_deposit,
                        -- FUND
                        (
                            COALESCE((
                                SELECT SUM(amount)
                                FROM member_deposit
                                WHERE status = 'complete' AND member_id = m.id
                            ), 0)
                            +
                            COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id AND (jenis = 'balance' OR jenis = 'comission') AND withdraw_type = 'usdt'
                            ), 0)
                            -
                            COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id
                                  AND (
                                    (jenis = 'withdraw' AND status <> 'rejected' AND withdraw_type IN ('usdt', 'usdc','fiat'))
                                    OR (jenis = 'trade' AND withdraw_type = 'usdt')
                                  )
                            ), 0)
                        ) AS fund,
                    
                        -- TRADE (USDT)
                        -- if superadmin
                    CASE WHEN m.role = 'superadmin' and m.id = 1 THEN
                    FLOOR(
                        (
                        /* 1) all master_wallet (global) */
                        COALESCE((SELECT SUM(master_wallet) FROM wallet), 0)
                        + COALESCE((SELECT SUM(commission) FROM member_deposit WHERE upline_id IS NULL), 0)
                        /* 2) + this member’s client_wallet */
                        + COALESCE((SELECT SUM(client_wallet)
                                    FROM wallet
                                    WHERE member_id = m.id), 0)
                        /* 3) − unclosed Buys for this member */
                        - COALESCE(
                            (
                                SELECT SUM(ms.amount_usdt)
                                FROM member_sinyal ms
                                JOIN sinyal s ON s.id = ms.sinyal_id
                                WHERE
                                ms.member_id = m.id
                                AND s.type   LIKE 'Buy%'
                                AND s.status != 'canceled'
                                /* exclude any Buy whose pair_id has already been Sold by this member */
                                AND NOT EXISTS (
                                    SELECT 1
                                    FROM member_sinyal ms2
                                    JOIN sinyal     s2 ON s2.id = ms2.sinyal_id
                                    WHERE
                                    ms2.member_id = ms.member_id
                                    AND s2.type    LIKE 'Sell%'
                                    AND s2.status  = 'filled'
                                    AND s2.pair_id = s.pair_id
                                )
                            ),
                            0
                            )
                        /* 4) + this member’s trade-withdrawals (USDT) */
                        + COALESCE(
                            (SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id   = m.id
                                AND jenis       = 'trade'
                                AND withdraw_type = 'usdt'),
                            0
                            )
                        /* 5) − this member’s balance-withdrawals (USDT) */
                        - COALESCE(
                            (SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id   = m.id
                                AND jenis       = 'balance'
                                AND withdraw_type = 'usdt'),
                            0
                            )
                        ) * 100
                    ) / 100
                    ELSE -- else superadmin
                        COALESCE((
                            SELECT -SUM(master_wallet)
                                FROM wallet
                                WHERE member_id = m.id
                            ), 0)
                            - COALESCE((
                                SELECT SUM(amount)
                                FROM member_commission
                                WHERE downline_id = m.id
                            ), 0)
                            - COALESCE((
                                SELECT SUM(
                                    CASE WHEN s.type LIKE 'Buy%' THEN ms.amount_usdt END
                                )
                                FROM member_sinyal ms
                                JOIN sinyal s ON s.id = ms.sinyal_id
                                WHERE ms.member_id = m.id AND s.status != 'canceled'
                            ), 0)
                            + COALESCE((
                                SELECT SUM(
                                    CASE WHEN s.type LIKE 'Sell%' THEN ms.amount_usdt END
                                )
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
                            ), 0) END AS trade,
                    
                        -- TRADE (BTC)
                        (
                            COALESCE((
                                SELECT SUM(
                                    CASE
                                        WHEN s.type LIKE 'Buy%'  THEN ms.amount_btc
                                        WHEN s.type LIKE 'Sell%' THEN -ms.amount_btc
                                        ELSE 0
                                    END
                                )
                                FROM member_sinyal ms
                                JOIN sinyal s ON s.id = ms.sinyal_id
                                WHERE ms.member_id = m.id AND s.status = 'filled'
                            ), 0)
                            + COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id AND jenis = 'trade' AND withdraw_type = 'btc'
                            ), 0)
                            - COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id AND jenis = 'balance' AND withdraw_type = 'btc'
                            ), 0)
                        ) AS trade_btc,
                    
                        -- COMMISSION
                        (
                            COALESCE((
                                SELECT SUM(md.commission)
                                FROM member_deposit md
                                JOIN member m2 ON md.member_id = m2.id
                                WHERE md.upline_id = m.id AND md.status = 'complete'
                            ), 0)
                            +
                            COALESCE((
                                SELECT SUM(ms.amount)
                                FROM member_commission ms
                                JOIN member m2 ON m2.id = ms.downline_id
                                WHERE ms.member_id = m.id
                            ), 0)
                            -
                            COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id AND status <> 'rejected' AND jenis = 'comission' AND withdraw_type = 'usdt'
                            ), 0)
                        ) AS comission,
                    
                        -- REFERRALS
                        COALESCE(COUNT(r.id), 0) AS referral
                    
                    FROM member m
                    LEFT JOIN member r
                        ON r.id_referral = m.id
                       AND r.status IN ('active', 'referral')
                       AND r.is_delete = FALSE
                    WHERE m.is_delete = FALSE
                    GROUP BY
                        m.role,
                        m.id,
                        m.email,
                        m.refcode,
                        m.created_at,
                        m.status;
";

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
    
    public function getby_id($member_id){
        $sql = "SELECT * FROM member
                WHERE
                    id = ?
                LIMIT
                    1";

        $query = $this->db->query($sql, [$member_id])->getRow();
        if (!$query) {
            return (object) [
                "code"    => "404",
                "message" => "User not found"
            ];
        }

        return (object) [
            "code"    => "200",
            "message" => $query
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
                'id'       => $id,
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

    public function update_data($mdata)
    {
        try {
            $member = $this->db->table("member");
            $member->updateBatch($mdata, 'id');
    
            return (object) [
                'success'  => true,
                'code' => 200,
                'message' => 'User data updated'
            ];
        } catch (\Exception $e) {
            return (object) [
                'success'  => false,
                'code'    => $e->getCode(),
                'message' => 'An error occurred: ' . $e->getMessage()
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
                        ref.email as upline_referral,
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
                                (jenis = 'withdraw' AND status <> 'rejected' AND (withdraw_type = 'usdt' or withdraw_type = 'usdc' or withdraw_type='fiat'))
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
                        COALESCE((SELECT -SUM(master_wallet) FROM wallet WHERE member_id = m.id), 0)
                        - COALESCE((
                            SELECT SUM(amount)
                            FROM member_commission
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
                        ), 0) AS trade_usdt,                      
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
                        member m LEFT JOIN member ref ON m.id_referral=ref.id
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

    public function get_downline_byId($id_member = NULL)
    {
        try {
            if($id_member === NULL) {
                $sql = "SELECT *
                        FROM member m
                        WHERE m.id_referral IS NULL
                           AND m.is_delete=0";
                $query = $this->db->query($sql)->getResult();
            } else {
                $sql = "SELECT *
                        FROM member m
                        WHERE m.id_referral = ?
                           AND m.is_delete=0;

                        ";
                $query = $this->db->query($sql, [$id_member])->getResult();
            }

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
    
    public function get_downlinedepo($id_member){
         try {
                $sql = "SELECT m.email, md.commission as komisi
                        FROM member_deposit md INNER JOIN member m
                        ON md.member_id=m.id
                        WHERE md.upline_id = ?
                           AND md.status='complete';

                        ";
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
                            CASE 
                                WHEN SUBSTRING_INDEX(s.type, ' ', 1) = 'buy' THEN NULL
                                ELSE w.client_wallet
                            END AS profit,
                            s.entry_price,
                            s.created_at AS date,
                            CASE
                                WHEN s.status = 'pending' THEN 0
                                ELSE ms.amount_btc
                            END AS amount_btc,
                            ms.amount_usdt,
                            SUBSTRING_INDEX(s.type, ' ', 1) AS position
                        FROM sinyal s
                        INNER JOIN member_sinyal ms ON ms.sinyal_id = s.id
                        LEFT JOIN wallet w ON w.member_id = ms.member_id AND w.order_id = s.order_id
                        WHERE ms.member_id = ? AND s.status = 'filled'

                        UNION ALL

                        -- order pending
                        SELECT
                            NULL as profit,
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

    public function history_trades()
    {
        try {
            $sql = " SELECT
                        s.status,
                        s.entry_price,
                        s.created_at AS date,
                        SUM(
                            CASE
                                WHEN s.status = 'pending' THEN 0
                                ELSE ms.amount_btc
                            END
                        ) AS amount_btc,
                        SUM(ms.amount_usdt) as amount_usdt,
                        SUBSTRING_INDEX(s.type, ' ', 1) AS position
                    FROM
                        sinyal s
                        INNER JOIN member_sinyal ms ON ms.sinyal_id = s.id
                    WHERE
                        s.status != 'canceled'
                    GROUP BY
                        s.id
                    UNION ALL-- order pending/canceled
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
                        s.status = 'pending'
                    GROUP BY
                        s.id";
            $query = $this->db->query($sql)->getResult();

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

/*    public function getTotal_balance() {
        try {

            $sql = "SELECT
                    ROUND(SUM(t1.fund_balance), 2) AS fund_usdt,
                    SUM(t1.trade_balance) AS trade_usdt,
                    0 AS fund_btc,
                    ROUND(SUM(t1.trade_btc), 6) AS trade_btc,
                    ROUND(t2.commission, 2) AS commission,
                    ROUND((
                        SELECT 
                            SUM(
                                CASE 
                                    WHEN m.id_referral IS NULL THEN w.master_wallet - (0.1 * w.client_wallet)
                                    ELSE w.master_wallet
                                END
                            )
                        FROM wallet w
                        JOIN member m ON w.member_id = m.id
                    ), 2) AS master_profit,

                    ROUND(t3.total_profit, 2) AS total_profit,
                    ROUND((
                        SELECT 
                            SUM(master_wallet) FROM wallet w
                    ),2) as master_balance                    
                FROM
                    (
                        SELECT
                            m.id AS member_id,
                            -- fund_balance calculation
                            COALESCE((
                                SELECT SUM(amount)
                                FROM member_deposit
                                WHERE status = 'complete' AND member_id = m.id
                            ), 0)
                            + COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id
                                AND (jenis = 'balance' OR jenis = 'comission')
                                AND withdraw_type = 'usdt'
                            ), 0)
                            - COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id
                                AND (
                                    (jenis = 'withdraw' AND status <> 'rejected' AND (withdraw_type = 'usdt' OR withdraw_type = 'usdc' OR withdraw_type='fiat'))
                                    OR (jenis = 'trade' AND withdraw_type = 'usdt')
                                )
                            ), 0) AS fund_balance,
                
                            -- trade_balance calculation
                            -- superadmin
                            CASE WHEN m.role = 'superadmin' and m.id = 1 THEN
                                COALESCE((SELECT SUM(master_wallet) FROM wallet), 0)
                                + COALESCE((SELECT SUM(client_wallet)
                                            FROM wallet
                                            WHERE member_id = m.id), 0)
                                - COALESCE((
                                    SELECT SUM(ms.amount_usdt)
                                    FROM member_sinyal ms
                                    JOIN sinyal s ON s.id = ms.sinyal_id
                                    WHERE
                                        ms.member_id = m.id
                                        AND s.type LIKE 'Buy%'
                                        AND s.status = 'filled'
                                        AND NOT EXISTS (
                                            SELECT 1
                                            FROM member_sinyal ms2
                                            JOIN sinyal s2 ON s2.id = ms2.sinyal_id
                                            WHERE
                                                ms2.member_id = ms.member_id
                                                AND s2.type LIKE 'Sell%'
                                                AND s2.status = 'filled'
                                                AND s2.pair_id = s.pair_id
                                        )
                                ), 0)
                                + COALESCE((
                                    SELECT SUM(amount)
                                    FROM withdraw
                                    WHERE member_id = m.id
                                        AND jenis = 'trade'
                                        AND withdraw_type = 'usdt'
                                ), 0)
                                - COALESCE((
                                    SELECT SUM(amount)
                                    FROM withdraw
                                    WHERE member_id = m.id
                                        AND jenis = 'balance'
                                        AND withdraw_type = 'usdt'
                                ), 0)
                            
                            ELSE
                            -- else superadmin
                            COALESCE((SELECT -SUM(master_wallet) FROM wallet WHERE member_id = m.id), 0)
                            - COALESCE((SELECT SUM(amount) FROM member_commission WHERE member_id = m.id), 0)
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
                            ), 0) END AS trade_balance,
                
                            -- trade_btc calculation
                            COALESCE((
                                SELECT SUM(CASE
                                    WHEN s.type LIKE 'Buy%' THEN ms.amount_btc
                                    WHEN s.type LIKE 'Sell%' THEN -ms.amount_btc
                                    ELSE 0 END)
                                FROM member_sinyal ms
                                JOIN sinyal s ON s.id = ms.sinyal_id
                                WHERE ms.member_id = m.id AND s.status = 'filled'
                            ), 0)
                            + COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id AND jenis = 'trade' AND withdraw_type = 'btc'
                            ), 0)
                            - COALESCE((
                                SELECT SUM(amount)
                                FROM withdraw
                                WHERE member_id = m.id AND jenis = 'balance' AND withdraw_type = 'btc'
                            ), 0) AS trade_btc
                        FROM member m
                    ) AS t1,
                    (
                        -- Commission summary
                        SELECT SUM(commission) AS commission
                        FROM (
                            SELECT 
                                md.commission AS commission
                            FROM member_deposit md
                            JOIN member m ON md.member_id = m.id
                            WHERE md.status = 'complete'
                
                            UNION ALL
                
                            SELECT 
                                -w.amount AS commission
                            FROM withdraw w
                            WHERE w.status <> 'rejected'
                              AND w.withdraw_type = 'usdt' AND w.jenis = 'comission'
                
                            UNION ALL
                
                            SELECT 
                                ms.amount AS commission
                            FROM member_commission ms
                            JOIN member m ON m.id = ms.downline_id
                        ) AS commission_data
                    ) AS t2,
                    (
                        SELECT
                            ROUND(SUM(ms_sell.amount_usdt - ms_buy.amount_usdt), 2) AS total_profit
                        FROM
                            sinyal s_sell
                        JOIN member_sinyal ms_sell ON ms_sell.sinyal_id = s_sell.id
                        JOIN sinyal s_buy ON s_buy.pair_id = s_sell.pair_id AND s_buy.type LIKE 'Buy%'
                        JOIN member_sinyal ms_buy ON ms_buy.sinyal_id = s_buy.id AND ms_buy.member_id = ms_sell.member_id
                        WHERE
                            s_sell.type LIKE 'Sell%'
                            AND s_sell.status = 'filled'
                    ) AS t3";
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
*/    
      public function getTotal_balance() {
        try {

            $sql = "SELECT
                        ROUND(SUM(t1.fund_balance), 2) AS fund_usdt,
                        SUM(t1.trade_balance) AS trade_usdt,
                        0 AS fund_btc,
                        ROUND(SUM(t1.trade_btc), 6) AS trade_btc,
                        ROUND(t2.commission, 2) AS commission,
                    
                        -- Master profit exclude superadmin
                        ROUND((
                            SELECT SUM(
                                       CASE 
                                           WHEN m.id_referral IS NULL THEN w.master_wallet - (0.1 * w.client_wallet)
                                           ELSE w.master_wallet
                                       END
                                   )
                            FROM wallet w
                            JOIN member m ON w.member_id = m.id
                            WHERE w.member_id <> 1
                        ), 2) AS master_profit,
                    
                        -- Master trade khusus user id=1
                        (
                            FLOOR(
                                (
                                    /* 1) all master_wallet (global) */
                                    COALESCE((SELECT SUM(master_wallet) FROM wallet), 0)
                                    + COALESCE((
                                        SELECT SUM(commission)
                                        FROM member_deposit
                                        WHERE upline_id IS NULL
                                          AND status = 'complete'
                                    ), 0)
                        
                                    /* 2) + this member’s client_wallet */
                                    + COALESCE((
                                        SELECT SUM(client_wallet)
                                        FROM wallet
                                        WHERE member_id = 1
                                    ), 0)
                        
                                    /* 3) − unclosed Buys for this member */
                                    - COALESCE((
                                        SELECT SUM(ms.amount_usdt)
                                        FROM member_sinyal ms
                                        JOIN sinyal s ON s.id = ms.sinyal_id
                                        WHERE ms.member_id = 1
                                          AND s.type LIKE 'Buy%'
                                          AND s.status != 'canceled'
                                          /* exclude any Buy whose pair_id has already been Sold by this member */
                                          AND NOT EXISTS (
                                              SELECT 1
                                              FROM member_sinyal ms2
                                              JOIN sinyal s2 ON s2.id = ms2.sinyal_id
                                              WHERE ms2.member_id = ms.member_id
                                                AND s2.type LIKE 'Sell%'
                                                AND s2.status  = 'filled'
                                                AND s2.pair_id = s.pair_id
                                          )
                                    ), 0)
                        
                                    /* 4) + this member’s trade-withdrawals (USDT) */
                                    + COALESCE((
                                        SELECT SUM(amount)
                                        FROM withdraw
                                        WHERE member_id     = 1
                                          AND jenis         = 'trade'
                                          AND withdraw_type = 'usdt'
                                    ), 0)
                        
                                    /* 5) − this member’s balance-withdrawals (USDT) */
                                    - COALESCE((
                                        SELECT SUM(amount)
                                        FROM withdraw
                                        WHERE member_id     = 1
                                          AND jenis         = 'balance'
                                          AND withdraw_type = 'usdt'
                                    ), 0)
                                ) * 100
                            ) / 100
                        ) AS master_trade,
                    
                        ROUND(t3.total_profit, 2) AS total_profit,
                    
                        -- Master balance exclude superadmin
                        ROUND((
                            SELECT SUM(master_wallet) 
                            FROM wallet w 
                            WHERE w.member_id <> 1
                        ),2) as master_balance    
                    
                    FROM
                        (
                            SELECT
                                m.id AS member_id,
                    
                                -- fund_balance
                                COALESCE((
                                    SELECT SUM(amount)
                                    FROM member_deposit
                                    WHERE status = 'complete' AND member_id = m.id AND member_id<>1
                                ), 0)
                                + COALESCE((
                                    SELECT SUM(amount)
                                    FROM withdraw
                                    WHERE member_id = m.id
                                      AND (jenis = 'balance' OR jenis = 'comission')
                                      AND withdraw_type = 'usdt'  AND member_id<>1
                                ), 0)
                                - COALESCE((
                                    SELECT SUM(amount)
                                    FROM withdraw
                                    WHERE member_id = m.id
                                      AND (
                                            (jenis = 'withdraw' AND status <> 'rejected' AND withdraw_type IN ('usdt','usdc','fiat'))
                                            OR (jenis = 'trade' AND withdraw_type = 'usdt')
                                          )  AND member_id<>1
                                ), 0) AS fund_balance,
                    
                                -- trade_balance (exclude superadmin)
                                CASE WHEN m.role = 'superadmin' AND m.id = 1 THEN
                                    0
                                ELSE
                                    COALESCE((SELECT -SUM(master_wallet) FROM wallet WHERE member_id = m.id AND member_id <> 1), 0)
                                    - COALESCE((SELECT SUM(amount) FROM member_commission WHERE member_id = m.id), 0)
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
                                END AS trade_balance,
                    
                                -- trade_btc
                                COALESCE((
                                    SELECT SUM(CASE
                                                   WHEN s.type LIKE 'Buy%' THEN ms.amount_btc
                                                   WHEN s.type LIKE 'Sell%' THEN -ms.amount_btc
                                                   ELSE 0 END)
                                    FROM member_sinyal ms
                                    JOIN sinyal s ON s.id = ms.sinyal_id
                                    WHERE ms.member_id = m.id AND s.status = 'filled'
                                ), 0)
                                + COALESCE((
                                    SELECT SUM(amount)
                                    FROM withdraw
                                    WHERE member_id = m.id AND jenis = 'trade' AND withdraw_type = 'btc'
                                ), 0)
                                - COALESCE((
                                    SELECT SUM(amount)
                                    FROM withdraw
                                    WHERE member_id = m.id AND jenis = 'balance' AND withdraw_type = 'btc'
                                ), 0) AS trade_btc
                    
                            FROM member m
                        ) AS t1,
                    
                        (
                            -- Commission summary (exclude member tanpa upline)
                            SELECT SUM(commission) AS commission
                            FROM (
                                SELECT md.commission AS commission
                                FROM member_deposit md
                                JOIN member m ON md.member_id = m.id
                                WHERE md.status = 'complete'
                                  AND m.id_referral IS NOT NULL
                    
                                UNION ALL
                    
                                SELECT -w.amount AS commission
                                FROM withdraw w
                                JOIN member m ON w.member_id = m.id
                                WHERE w.status <> 'rejected'
                                  AND w.withdraw_type = 'usdt'
                                  AND w.jenis = 'comission'
                                  AND m.id_referral IS NOT NULL
                    
                                UNION ALL
                    
                                SELECT ms.amount AS commission
                                FROM member_commission ms
                                JOIN member m ON m.id = ms.downline_id
                                WHERE m.id_referral IS NOT NULL
                            ) AS commission_data
                        ) AS t2,
                    
                        (
                            SELECT ROUND(SUM(ms_sell.amount_usdt - ms_buy.amount_usdt), 2) AS total_profit
                            FROM sinyal s_sell
                            JOIN member_sinyal ms_sell ON ms_sell.sinyal_id = s_sell.id
                            JOIN sinyal s_buy ON s_buy.pair_id = s_sell.pair_id AND s_buy.type LIKE 'Buy%'
                            JOIN member_sinyal ms_buy ON ms_buy.sinyal_id = s_buy.id AND ms_buy.member_id = ms_sell.member_id
                            WHERE s_sell.type LIKE 'Sell%'
                              AND s_sell.status = 'filled'
                        ) AS t3;
";
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

    public function list_transaction($id){
        try {

            $sql = "WITH buy_signals AS ( 
                    SELECT
                        s.type as buy_type,
                        s.id AS buy_id,
                        s.order_id AS buy_order_id,
                        s.pair_id,
                        s.entry_price AS buy_price,
                        s.created_at AS buy_time
                    FROM sinyal s
                    WHERE s.type LIKE 'Buy%' AND s.status = 'filled' AND s.is_deleted = 'no'
                ),
                sell_signals AS (
                    SELECT
                        s.id AS sell_id,
                        s.order_id AS sell_order_id,
                        s.pair_id,
                        s.entry_price AS sell_price,
                        s.created_at AS sell_time
                    FROM sinyal s
                    WHERE s.type LIKE 'Sell%' AND s.status = 'filled' AND s.is_deleted = 'no'
                ),
                paired_signals AS (
                    SELECT
                        b.buy_id,
                        b.buy_order_id,
                        b.pair_id,
                        b.buy_type,
                        b.buy_price,
                        s.sell_id,
                        s.sell_price,
                        s.sell_time,
                        s.sell_order_id
                    FROM buy_signals b
                    LEFT JOIN sell_signals s
                        ON s.pair_id = b.pair_id AND s.sell_time > b.buy_time
                ),
                member_amounts AS (
                    SELECT
                        sinyal_id,
                        amount_btc,
                        amount_usdt,
                        SUM(amount_btc) AS total_btc,
                        SUM(amount_usdt) AS total_usdt
                    FROM member_sinyal
                    WHERE member_id=?
                    GROUP BY sinyal_id
                ),
                wallet_profits AS (
                    SELECT
                        order_id,
                        SUM(master_wallet) AS master_profit,
                        SUM(client_wallet) AS client_profit
                    FROM wallet
                    WHERE member_id=?
                    GROUP BY order_id
                ),
                commission_totals AS (
                    SELECT
                        order_id,
                        SUM(amount) AS total_commission
                    FROM member_commission
                    WHERE downline_id=?
                    GROUP BY order_id
                )
                SELECT
                m.amount_btc,
                m.amount_usdt,
                    p.buy_id,
                    p.buy_order_id,
                    p.pair_id,
                    p.buy_price,
                    p.sell_price,
                    p.buy_type as buy_type,
                    m.total_btc AS buy_total_btc,
                    m.total_usdt AS buy_total_usdt,
                    msell.total_usdt AS sell_total_usdt,
                    w.master_profit,
                    w.client_profit,
                    c.total_commission
                FROM paired_signals p
                LEFT JOIN member_amounts m ON m.sinyal_id = p.buy_id
                LEFT JOIN member_amounts msell ON msell.sinyal_id = p.sell_id
                LEFT JOIN wallet_profits w ON w.order_id = p.sell_order_id
                LEFT JOIN commission_totals c ON c.order_id = p.sell_order_id";
            $query = $this->db->query($sql,[$id,$id,$id])->getResult();

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
    
    public function getMember_sinyal($signal_id){
        try{
            $sql="SELECT * FROM member_sinyal WHERE sinyal_id=?";
            $query = $this->db->query($sql, [$signal_id])->getResult();
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

    public function remove_referral($idmember){
        // Reset referral for all members whose referral is this one
        try{
            $this->db->table("member")
                ->set("id_referral", null)
                ->where("id_referral", $idmember)
                ->update();
            
            // Reset refcode and role for this member
            $this->db->table("member")
                ->set([
                    "refcode" => null,
                    "role"    => "member"   // string, not bare identifier
                ])
                ->where("id", $idmember)
                ->update();
            return (object) [
                'code' => 200,
                'message' => "Successfully Updated"
            ];
        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred.' .$e
            ];
        }
    }


    /*    public function list_transactions(){
        try {

            $sql = "WITH buy_signals AS ( 
                    SELECT
                        s.id AS buy_id,
                        s.order_id AS buy_order_id,
                        s.pair_id,
                        s.entry_price AS buy_price,
                        s.created_at AS buy_time
                    FROM sinyal s
                    WHERE s.type LIKE 'Buy%' AND s.status = 'filled' AND s.is_deleted = 'no'
                ),
                sell_signals AS (
                    SELECT
                        s.id AS sell_id,
                        s.order_id AS sell_order_id,
                        s.pair_id,
                        s.entry_price AS sell_price,
                        s.created_at AS sell_time
                    FROM sinyal s
                    WHERE s.type LIKE 'Sell%' AND s.status = 'filled' AND s.is_deleted = 'no'
                ),
                paired_signals AS (
                    SELECT
                        b.buy_id,
                        b.buy_order_id,
                        b.pair_id,
                        b.buy_price,
                        s.sell_id,
                        s.sell_price,
                        s.sell_time,
                        s.sell_order_id
                    FROM buy_signals b
                    LEFT JOIN sell_signals s
                        ON s.pair_id = b.pair_id AND s.sell_time > b.buy_time
                ),
                member_amounts AS (
                    SELECT
                        sinyal_id,
                        SUM(amount_btc) AS total_btc,
                        SUM(amount_usdt) AS total_usdt
                    FROM member_sinyal
                    GROUP BY sinyal_id
                ),
                wallet_profits AS (
                    SELECT
                        order_id,
                        SUM(master_wallet) AS master_profit,
                        SUM(client_wallet) AS client_profit
                    FROM wallet
                    GROUP BY order_id
                ),
                commission_totals AS (
                    SELECT
                        order_id,
                        SUM(amount) AS total_commission
                    FROM member_commission
                    GROUP BY order_id
                )
                SELECT
                    p.buy_id,
                    p.buy_order_id,
                    p.pair_id,
                    p.buy_price,
                    p.sell_price,
                    m.total_btc AS buy_total_btc,
                    m.total_usdt AS buy_total_usdt,
                    msell.total_usdt AS sell_total_usdt,
                    w.master_profit,
                    SUM(w.client_profit) as client_profit,
                    c.total_commission
                FROM paired_signals p
                LEFT JOIN member_amounts m ON m.sinyal_id = p.buy_id
                LEFT JOIN member_amounts msell ON msell.sinyal_id = p.sell_id
                LEFT JOIN wallet_profits w ON w.order_id = p.sell_order_id
                LEFT JOIN commission_totals c ON c.order_id = p.sell_order_id
                GROUP BY p.buy_id, p.sell_id;";
            $query = $this->db->query($sql)->getResult();

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
*/
    public function get_number_by_email($email)
    {
        try {
            $sql =
                "SELECT phone_number FROM member WHERE email=?";
            $query = $this->db->query($sql, [$email])->getRow();
            return (object) [
                'code' => 200,
                'message' => $query
            ];
        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred.' . $e
            ];
        }
    }
}
