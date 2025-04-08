<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;
use Hashids\Hashids;

/*----------------------------------------------------------
    Modul Name  : Database member_commission
    Desc        : Menyimpan data member, proses member
    Sub fungsi  : 
        - getby_id          : Mendapatkan data user dari username
        - change_password   : Ubah password
------------------------------------------------------------*/


class Mdl_commission extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'member_commssion';
    protected $primaryKey = 'id';

    protected $allowedFields = ['member_id', 'downline_id', 'amount'];

    protected $returnType = 'array';
    protected $useTimestamps = true;
    
    public function add_balance($mdata) {
        try {
            $deposit = $this->db->table("member_commission");
            $deposit->insert($mdata);
    
            return (object) [
                'code'    => 201,
                'message' => 'Success: Commsission has been added.'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while processing.',
                'error'   => $e->getMessage()
            ];
        }
    }

    public function add_balances($mdata) {
        try {
            $deposit = $this->db->table("member_commission");
            $deposit->insertBatch($mdata);
    
            return (object) [
                'code'    => 201,
                'message' => 'Success: Commsission has been added.'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while processing.',
                'error'   => $e->getMessage()
            ];
        }
    }

    public function transfer($mdata) {
        $id_member = $mdata['id_member'];
        try {

            $available_commission = $this->db->query($this->getSql_commission(), [$id_member, $id_member, $id_member])->getRow();

            if(!$available_commission) {
                return (object) [
                    'code'    => 400,
                    'message' => 'Failed to get available commission.'
                ];
            }

            return (object) [
                'code'    => 201,
                'message' => 'Success: Commsission has been added.'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while processing.',
                'error'   => $e->getMessage()
            ];
        }
    }

    public function getBalance_byId($id_member)
    {
        try {
            $sql = "SELECT
                        COALESCE(comm.total_commission, 0)
                        - (COALESCE(wd.transfered, 0) * 2) AS balance
                    FROM (
                        SELECT SUM(commission) AS total_commission
                        FROM ({$this->getSql_commission()}) AS all_commission
                    ) AS comm
                    LEFT JOIN (
                        SELECT SUM(amount) AS transfered
                        FROM withdraw
                        WHERE member_id = ?
                    ) AS wd ON 1 = 1";

            $query = $this->db->query($sql, [$id_member, $id_member, $id_member, $id_member])->getRow();

        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An unexpected error occurred. Please try again later.' . $e
            ];
        }

        return (object) [
            'code' => 200,
            'message' => $query,
        ];
    }

    public function get_commission_byId($id_member)
    {
        try {

            $sql = $this->getSql_commission();
            $query = $this->db->query($sql, [$id_member,$id_member, $id_member])->getResult();

            if (!$query) {
                return (object) [
                    'code' => 200,
                    'message' => 'No active downline members found.',
                    'data'  => []
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
    
    protected function getSql_commission(): string
{
    return "SELECT 
            md.created_at as date,
            md.commission AS commission,
            CONCAT('referral commission from ', m.email) AS description,
            NULL AS status
        FROM 
            member_deposit md
        JOIN 
            member m ON md.member_id = m.id
        WHERE 
            m.id_referral = ?

        UNION ALL

        SELECT 
            w.requested_at as date,
            w.amount AS commission,
            CASE 
                WHEN w.jenis = 'trade' THEN 'Transfer to trade balance'
                WHEN w.jenis = 'balance' THEN 'Transfer to withdraw balance'
                ELSE CONCAT(w.jenis,' ',withdraw_type)
            END AS description,
            w.status
        FROM 
            withdraw w
        WHERE 
            w.member_id = ?
            AND w.status <> 'rejected'
            AND w.withdraw_type = 'usdt'
        GROUP BY 
            w.jenis, w.status

        UNION ALL

        SELECT
            ms.created_at as date,
            ms.amount as commission,
            CONCAT('trade commission from ', m.email) AS description,
            NULL as status
        FROM
            member_commission ms
        INNER JOIN 
            member m ON m.id = ms.member_id
        WHERE 
            ms.upline_id = ?
    ";
}

}
