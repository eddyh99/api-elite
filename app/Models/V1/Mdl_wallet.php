<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;
use Hashids\Hashids;

/*----------------------------------------------------------
    Modul Name  : Database member wallet
    Desc        : Menyimpan data member, proses member
    Sub fungsi  : 
        - getby_id          : Mendapatkan data user dari username
        - change_password   : Ubah password
------------------------------------------------------------*/


class Mdl_wallet extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'wallet';
    protected $primaryKey = 'id';

    protected $allowedFields = ['master_wallet', 'client_wallet', 'member_id', 'order_id'];

    protected $returnType = 'array';
    protected $useTimestamps = true;


    public function add_profits($mdata) {
        try {
            $deposit = $this->db->table("wallet");
            $deposit->insertBatch($mdata);
    
            return (object) [
                'code'    => 201,
                'message' => 'Success: profit has been added.'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while processing.',
                'error'   => $e->getMessage()
            ];
        }
    }

    public function getBalance_byIdMember($id_member) {
        try {
            $sql = "SELECT
                        COALESCE((
                            SELECT SUM(client_wallet)
                            FROM wallet
                            WHERE member_id = ?
                        ), 0) -- wallet

                        + COALESCE((
                            SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id = ?
                            AND jenis = 'trade'
                        ) 
                        - COALESCE((
                            SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id = ?
                            AND jenis = 'balance' AND withdraw_type = 'fiat'
                        ), 0) , 0) -- trade
                            AS usdt,
                        0 as btc"; 
            $query = $this->db->query($sql, [$id_member, $id_member, $id_member])->getRow();

            return (object) [
                'code' => 200,
                'message' => $query
            ];

        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred.'
            ];
        }
    }
    
}
