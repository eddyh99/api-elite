<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;
use Hashids\Hashids;

/*----------------------------------------------------------
    Modul Name  : Database member_deposit
    Desc        : Menyimpan data member, proses member
    Sub fungsi  : 
        - getby_id          : Mendapatkan data user dari username
        - change_password   : Ubah password
------------------------------------------------------------*/


class Mdl_deposit extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'member_deposit';
    protected $primaryKey = 'id';

    protected $allowedFields = ['member_id', 'amount'];

    protected $returnType = 'array';
    protected $useTimestamps = true;

    
    public function add_balance($mdata) {
        try {
            $deposit = $this->db->table("member_deposit");
            $deposit->insert($mdata);
    
            return (object) [
                'code'    => 201,
                'message' => 'Success: Deposit has been added.'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while processing the deposit. Please try again later.',
                'error'   => $e->getMessage()
            ];
        }
    }

    public function get_amount()
    {
        try {
            $sql = "SELECT
                        COALESCE(SUM(amount), 0) / 4 AS amount
                    FROM
                        member_deposit
                    WHERE
                        status = 'complete';
                    ";
            $query = $this->db->query($sql)->getRow();

            return (object) [
                'code' => 200,
                'message' => $query->amount
            ];

        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred.' .$e
            ];
        }
    }

    // balance trade
    public function getAmount_member() {
        try {
            $sql = "SELECT
                        ms.member_id,
                        m.id_referral AS upline,
                        SUM(ms.amount) + COALESCE(
                            (SELECT SUM(client_wallet) FROM wallet w WHERE w.member_id = ms.member_id), 
                            0
                        ) AS total_amount
                    FROM
                        member_deposit ms
                        INNER JOIN member m ON m.id = ms.member_id
                    WHERE
                        ms.status = 'complete'
                    GROUP BY
                        ms.member_id";
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

    public function getBalance_byIdMember($id_member) {
        try {
            $sql = "SELECT
                    COALESCE((
                        SELECT SUM(amount)
                        FROM member_deposit
                        WHERE status = 'complete' AND member_id = ?
                    ), 0) -- deposit
                    +
                    COALESCE((
                        SELECT SUM(amount)
                        FROM withdraw
                        WHERE member_id = ? AND jenis = 'balance'
                    ), 0) -- balance
                    -
                    COALESCE((
                        SELECT SUM(amount)
                        FROM withdraw
                        WHERE member_id = ? AND jenis IN ('withdraw', 'trade')
                    ), 0) AS usdt, -- already withdrawn
                    
                    0 as btc"; 
            $query = $this->db->query($sql, [$id_member, $id_member, $id_member])->getRow();

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
    
    public function update_status($mdata) {
        try {
            // Update status berdasarkan email member
            $sql = "UPDATE member_deposit 
                    INNER JOIN member ON member.id = member_deposit.member_id 
                    SET member_deposit.status = ? 
                    WHERE member_deposit.invoice = ?";
    
            $this->db->query($sql, [$mdata['status'], $mdata['invoice']]);
            $affectedRows = $this->db->affectedRows();

            // Jika update gagal
            if (!$affectedRows) {
                return (object) array(
                    "code"    => 400,
                    "message" => "Failed to update deposit status"
                );
            }
    
        } catch (\Throwable $th) {
            return (object) array(
                "code"    => 500,
                "message" => "An unexpected server error occurred"
            );
        }
    
        return (object) array(
            "code"    => 201,
            "message" => "Deposit has been updated successfully"
        );
    }  
    
        
    public function history($id_member) {
        try {
            // Update status berdasarkan email member
            // $commission = new Mdl_commission();
            // $sql = $commission->query_commission();
            $sql = '';

            $sql .= "-- UNION ALL
                    SELECT
                        md.created_at as date,
                        md.amount AS commission,
                        CONCAT('deposit') AS description,
                        md.status
                    FROM
                        member_deposit md
                        JOIN member m ON md.member_id = m.id
                    WHERE
                        md.member_id = ?";
            $query = $this->db->query($sql, [$id_member])->getResult();

        if(!$query) {
            return (object) array(
                "code"    => 200,
                "message" => []
            );
        }

        } catch (\Exception $e) {
            return (object) array(
                "code"    => 500,
                "message" => "An unexpected server error occurred" .$e
            );
        }
    
        return (object) array(
            "code"    => 200,
            "message" => $query
        );
    }   
    
}
