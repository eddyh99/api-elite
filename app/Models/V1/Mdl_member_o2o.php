<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;
use Hashids\Hashids;

/*----------------------------------------------------------
    Modul Name  : Database Member One to One
    Desc        : Menyimpan data member, proses member one to one
------------------------------------------------------------*/


class Mdl_member_o2o extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'member_o2o';
    protected $primaryKey = 'id';

    protected $allowedFields = ['email', 'passwd', 'id_referral', 'timezone', 'otp', 'role', 'status', 'ip_addr', 'is_delete', 'api_key', 'api_secret'];

    protected $returnType = 'array';
    protected $useTimestamps = true;


    public function get_all_old()
    {

        try {

            $sql = "SELECT
                        member.role,
                        member.id,
                        member.email,
                        member.refcode,
                        member.created_at,
                        member.status,
                        s.start_date,
                        s.end_date,
                        COALESCE(s.initial_capital, 0) AS initial_capital,
                        COALESCE(COUNT(r.id), 0) AS referral
                    FROM
                        member
                        LEFT JOIN (
                            SELECT
                                member_id,
                                start_date,
                                end_date,
                                initial_capital
                            FROM
                                subscription
                            WHERE
                                (member_id, start_date) IN (
                                    SELECT
                                        member_id,
                                        MAX(start_date)
                                    FROM
                                        subscription
                                    GROUP BY
                                        member_id
                                )
                        ) s ON s.member_id = member.id
                        LEFT JOIN member r ON r.id_referral = member.id
                        AND r.status = 'active'
                    WHERE
                        member.is_delete = FALSE
                    AND
                        member.role = 'member'
                    GROUP BY
                        member.id,
                        member.email,
                        member.refcode,
                        member.created_at,
                        member.status,
                        s.start_date,
                        s.end_date";

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

    public function get_all()
    {
        try {
            $sql = "
        SELECT
            m.role,
            m.id,
            m.email,
            m.refcode,
            m.created_at,
            m.status,
            MAX(s.start_date) AS start_date,
            MAX(s.end_date) AS end_date,
            COALESCE(MAX(s.initial_capital), 0) AS initial_capital,
            COUNT(r.id) AS referral
        FROM
            member_o2o m
            LEFT JOIN subscription s 
                ON s.member_o2o_id = m.id
            LEFT JOIN member_o2o r 
                ON r.id_referral = m.id
                AND r.status = 'active'
        WHERE
            m.is_delete = FALSE
            AND m.role = 'member'
        GROUP BY
            m.id,
            m.email,
            m.refcode,
            m.created_at,
            m.status,
            m.role
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
                'message' => $e->getMessage()
            ];
        }

        return (object) [
            "code"    => 200,
            "message" => $query
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

    public function get_freemember()
    {
        try {

            $sql = "SELECT
                        member.email,
                        member.refcode,
                        s.start_date,
                        s.end_date
                    FROM
                        member
                        INNER JOIN subscription s ON s.member_id = member.id
                    WHERE
                        is_delete = FALSE
                        AND member.status != 'disabled'
                        AND s.status = 'free'";
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

    public function getby_email($email)
    {
        $sql = "
            SELECT
                m.*,
                mr.access,
                s.initial_capital,
                s.status AS subscription_status,
                s.start_date,
                s.end_date
            FROM
                member_o2o m
                LEFT JOIN subscription s ON m.id = s.member_o2o_id
                LEFT JOIN member_o2o_role mr ON mr.member_o2o_id = m.id
            WHERE
                m.email = ?
            ORDER BY
                s.id DESC
            LIMIT
                1;
        ";

        $query = $this->db->query($sql, [$email])->getRow();

        if (!$query) {
            return (object) [
                "code"    => "404",
                "message" => "User not found"
            ];
        }

        if ($query->status !== 'active') {
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
            $member = $this->db->table("member_o2o");
            $member->insert($data);
            $id     = $this->db->insertID();

            if (!$data['refcode']) {
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
                'message' => 'An error occurred.'
            ];
        }
    }

    private function generate_token($id)
    {
        $hashids = new Hashids('', 8, 'abcdefghijklmnopqrstuvwxyz1234567890');
        return $hashids->encode((int)$id, time(), rand());
    }

    public function freemember_add($mdata)
    {
        try {
            // Start Transaction
            $this->db->transBegin();

            // Table initialization
            $member = $this->db->table("member");
            $subscription = $this->db->table("subscription");

            // Insert into 'member'
            if (!$member->insert($mdata['member'])) {
                // Rollback if 'member' insertion fails
                $this->db->transRollback();
                return (object) [
                    "code"    => 400,
                    "message" => 'Failed to add new member' //"Gagal menyimpan data pembelian"
                ];
            }

            $id = $this->db->insertID();
            $mdata['refcode'] = array(
                "refcode"   => substr($this->generate_token($id), 0, 8),
            );
            $member->where("id", $id);
            $member->update($mdata['refcode']);

            // Add member_id to mdata
            $mdata['subscription']['member_id'] = $id;

            // Insert into 'subscription'
            if (!$subscription->insert($mdata['subscription'])) {
                // Rollback if 'subscription' insertion fails
                $this->db->transRollback();
                return (object) [
                    "code"    => 400,
                    "message" => "Failed to create a subscription for the member"
                ];
            }

            // Commit the transaction
            $this->db->transCommit();

            return (object) [
                "code"    => 201,
                "message" => "User has been successfully registered"
            ];
        } catch (\Exception $e) {
            // Rollback the transaction in case of an exception
            $this->db->transRollback();

            // Handle exception
            return (object) [
                "code"    => 500,
                "message" => "An internal server error occurred"
            ];
        }
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

    public function update_otp($mdata, $isgodmode)
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

            if (
                ($isgodmode && $member['role'] === 'member') ||
                (!$isgodmode && $member['role'] !== 'member')
            ) {
                return (object) [
                    'code'    => 400,
                    'message' => 'User denied.'
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

    public function reset_password($mdata)
    {
        try {
            // Validasi OTP dan email
            $valid = $this->where('email', $mdata['email'])
                ->where('otp', $mdata['otp'])
                ->first();

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
            $sql = "SELECT email, role FROM member_o2o where email = ?";
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

    public function getMembership()
    {
        try {
            $sql =
                "SELECT
                        COUNT(DISTINCT m.id) AS total_members,
                        SUM(
                            CASE
                                WHEN s.status = 'free' THEN 1
                                ELSE 0
                            END
                        ) AS total_free_members,
                        SUM(
                            CASE
                                WHEN s.status = 'active' THEN 1
                                ELSE 0
                            END
                        ) AS total_subscriptions
                    FROM
                        member_o2o m
                        LEFT JOIN subscription s ON s.member_o2o_id = m.id
                    WHERE m.is_delete = false AND m.role = 'member'";

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

    public function set_api($mdata)
    {
        try {

            // set api "member"
            $this->set($mdata)
                ->where('id', $mdata['id'])
                ->update();

            return (object) [
                'code'    => 200,
                'message' => 'API credentials have been successfully updated.'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'Failed to update API credentials.'
            ];
        }
    }

    public function detail_member_byEmail($email)
    {
        try {
            $sql = "SELECT
            m.id,
            CASE
                WHEN s.status = 'free' THEN 'Free Member'
                ELSE 'Normal Member'
            END AS membership_status,
            s.start_date,
            CASE
                WHEN s.status != 'expired' THEN 'active'
                ELSE 'expired'
            END AS subscription_status,
            DATEDIFF(s.end_date, s.start_date) AS subscription_plan,
            m.api_key,
            m.api_secret,
            m.refcode
        FROM
            member_o2o m
            LEFT JOIN (
                SELECT
                    member_o2o_id,
                    start_date,
                    end_date,
                    status
                FROM
                    subscription
                WHERE
                    (member_o2o_id, start_date) IN (
                        SELECT
                            member_o2o_id,
                            MAX(start_date)
                        FROM
                            subscription
                        GROUP BY
                            member_o2o_id
                    )
            ) s ON s.member_o2o_id = m.id
        WHERE
            m.email = ?";

            $query = $this->db->query($sql, [$email])->getRow();

            if (!$query) {
                return (object) [
                    'code'    => 404,
                    'message' => 'No member found with the given email address.'
                ];
            }
        } catch (\Throwable $th) {
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
                        m.email,
                        m.status,
                        COALESCE(s.commission, 0) as commission,
                        CASE
                            when s.status != 'expired' THEN 'active'
                            ELSE 'expired'
                        END AS 'subscription',
                        s.created_at
                    from
                        member_o2o m
                        LEFT JOIN subscription s ON s.member_o2o_id = m.id
                        AND s.start_date = (
                            SELECT
                                MAX(s2.start_date)
                            FROM
                                subscription s2
                            WHERE
                                s2.member_o2o_id = m.id
                        )
                    WHERE
                        m.id_referral = ?
                        and m.status = 'active'";
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
                // 'message' => $th->getMessage()
            ];
        }

        return (object) [
            'code' => 200,
            'message' => 'Downline members retrieved successfully..',
            'data'    => $query
        ];
    }

    public function admin_add($mdata)
    {
        try {
            // Start Transaction
            $this->db->transBegin();

            // Table initialization
            $member = $this->db->table("member");
            $access = $this->db->table("member_role");

            // Insert into 'member'
            if (!$member->insert($mdata['member'])) {
                // Rollback if 'admin' insertion fails
                $this->db->transRollback();
                return (object) [
                    "code"    => 400,
                    "message" => 'Failed to add admin. Something went wrong' //"Gagal menyimpan data admin"
                ];
            }

            $id = $this->db->insertID();

            // Add member_id to mdata
            $mdata['member_role']['member_id'] = $id;

            // Insert into 'member_role'
            if (!$access->insert($mdata['member_role'])) {
                // Rollback if 'member_role' insertion fails
                $this->db->transRollback();
                return (object) [
                    "code"    => 400,
                    "message" => "Failed to assign menu access"
                ];
            }

            // Commit the transaction
            $this->db->transCommit();

            return (object) [
                "code"    => 201,
                "message" => "Admin has been successfully added"
            ];
        } catch (\Exception $e) {
            // Rollback the transaction in case of an exception
            $this->db->transRollback();

            // Handle exception
            return (object) [
                "code"    => 500,
                "message" => "An internal server error occurred"
            ];
        }
    }

    public function get_referral_member()
    {
        try {
            $sql = "SELECT
                        member.id,
                        member.email,
                        member.refcode,
                        0 as commission,
                        COALESCE(COUNT(r.id), 0) AS referral,
                        'pn-global' as product
                    FROM
                        member
                        LEFT JOIN (
                            SELECT
                                member_id,
                                start_date,
                                end_date,
                                initial_capital
                            FROM
                                subscription
                            WHERE
                                (member_id, start_date) IN (
                                    SELECT
                                        member_id,
                                        MAX(start_date)
                                    FROM
                                        subscription
                                    GROUP BY
                                        member_id
                                )
                        ) s ON s.member_id = member.id
                        LEFT JOIN member r ON r.id_referral = member.id
                        AND r.status IN ('active', 'referral')
                    WHERE
                        member.is_delete = FALSE
                        AND member.role = 'referral'
                        -- AND member.status = 'referral'
                    GROUP BY
                        member.id,
                        member.email,
                        member.refcode,
                        member.created_at,
                        member.status,
                        s.start_date,
                        s.end_date";
            $query = $this->db->query($sql)->getResult();

            if (!$query) {
                return (object) [
                    'code' => 200,
                    'message' => [],
                ];
            }
        } catch (\Exception $e) {
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
}
