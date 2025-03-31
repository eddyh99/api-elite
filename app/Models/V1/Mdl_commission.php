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

    public function getBalance_byId($id_member) {
        try {
            $sql = "SELECT
                        COALESCE(SUM(amount), 0) AS balance
                    FROM
                        member_commission
                    WHERE
                        member_id = ?";
            $query = $this->db->query($sql, $id_member)->getRow();

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
