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
    public function get_all() {

        try {

            $sql = "SELECT
                        member.role,
                        member.id,
                        '-' AS subscription,
                        '0' AS initial_capital,
                        member.email,
                        member.refcode,
                        member.created_at,
                        member.status,
                        COALESCE(COUNT(r.id), 0) AS referral
                    FROM
                        member
                    LEFT JOIN member r
                        ON r.id_referral = member.id
                        AND r.status = 'active'
                        AND r.is_delete = FALSE
                    WHERE
                        member.is_delete = FALSE
                        AND member.role = 'member'
                    GROUP BY
                        member.role,
                        member.id,
                        member.email,
                        member.refcode,
                        member.created_at,
                        member.status";

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

    public function get_admin() {

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

    public function getby_email($email) {
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
            $member = $this->db->table("member");
            $member->insert($data);
            $id     = $this->db->insertID();

            $mdata = array(
                "refcode"   => substr($this->generate_token($id),0,8),
            );
            $member->where("id", $id);
            $member->update($mdata);
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
    

    private function generate_token($id) {
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

    public function update_otp($mdata) {
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

    public function activate($mdata) {
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
            $sql = "SELECT email, role FROM member where email = ?";
            $user = $this->db->query($sql, $mdata['email'])->getRow();

            if (!$user) {
                return (object) [
                    'code'    => 404,
                    'message' => 'User not found.'
                ];
            }

            if($user->role == 'superadmin') {
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
                        member m
                        LEFT JOIN subscription s ON s.member_id = m.id
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
    
    public function set_status($mdata) {
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
            m.refcode
        FROM
            member m
            LEFT JOIN (
                SELECT
                    member_id,
                    start_date,
                    end_date,
                    status
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
            ) s ON s.member_id = m.id
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
            $sql = "SELECT * 
                        FROM member 
                        
                    WHERE id_referral = ? 
                        AND is_delete = 0 
                        AND status = 'active'";
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
}
