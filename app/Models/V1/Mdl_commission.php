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
                        SUM(comm.commission) AS usdt
                    FROM
                        ({$this->getSql_commission()}) AS comm";

            $query = $this->db->query($sql, [$id_member, $id_member, $id_member])->getRow();

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

    public function get_commission_byId($id_member = NULL)
    {
        try {

            if($id_member === NULL) {
                $sql = "SELECT 
                            md.created_at as date,
                            md.commission AS commission,
                            CONCAT('deposit commission from ', m.email) AS description
                        FROM 
                            member_deposit md
                        JOIN 
                            member m ON md.member_id = m.id
                        WHERE 
                            md.upline_id IS NULL
                            AND md.status='complete'
                
                        UNION ALL
                        
                        SELECT 
                            w.created_at as date, 
                            w.client_wallet * 0.1 AS commission, 
                            CONCAT('trade commission from ', m.email) AS description
                        FROM wallet w
                        INNER JOIN member m 
                            ON w.member_id = m.id
                        LEFT JOIN member_commission mc 
                            ON mc.downline_id = w.member_id
                           AND mc.order_id = w.order_id
                        WHERE mc.id IS NULL;";
                $query = $this->db->query($sql)->getResult();
            } else {
                $sql = $this->getSql_commission();
                $query = $this->db->query($sql, [$id_member,$id_member, $id_member])->getResult();
            }

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
    
    private function getSql_commission(): string
{
    return "SELECT 
            md.created_at as date,
            md.commission AS commission,
            CONCAT('deposit commission from ', m.email) AS description,
            NULL AS status
        FROM 
            member_deposit md
        JOIN 
            member m ON md.member_id = m.id
        WHERE 
            md.upline_id = ?
            AND md.status='complete'

        UNION ALL
        
        SELECT 
            w.requested_at as date,
            -w.amount AS commission,
            CASE 
                WHEN w.jenis = 'comission' THEN 'Transfer to fund balance'
                ELSE CONCAT(w.jenis,' ',withdraw_type)
            END AS description,
            w.status
        FROM 
            withdraw w
        WHERE 
            w.member_id = ?
            AND w.status <> 'rejected'
            AND w.withdraw_type = 'usdt' AND jenis = 'comission'
        -- GROUP BY 
        --     w.jenis, w.status
        
        UNION ALL

        SELECT
            ms.created_at as date,
            ms.amount as commission,
            CONCAT('trade commission from ', m.email) AS description,
            NULL as status
        FROM
            member_commission ms
        INNER JOIN 
            member m ON m.id = ms.downline_id
        WHERE 
            ms.member_id = ?
    ";
}


    public function query_commission()
    {
        return $this->getSql_commission();
    }
    
    public function list_commission($id){
        $sql="SELECT CONCAT('Trade Commission FROM ',m.email) as description, amount as komisi
                FROM `member_commission` mc 
                INNER JOIN member m ON mc.downline_id=m.id 
            WHERE mc.member_id=?";
        $query=$this->db->query($sql,$id);
        return $query->getResult();
    }
}
