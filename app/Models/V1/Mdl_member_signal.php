<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;

/*----------------------------------------------------------
    Modul Name  : Database Member Signal
    Desc        : null
    Sub fungsi  : null

------------------------------------------------------------*/


class Mdl_member_signal extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'member_sinyal';
    protected $primaryKey = 'id';

    protected $allowedFields = ['amount_btc', 'member_id', 'sinyal_id'];

    protected $returnType = 'array';
    protected $useTimestamps = true;


    public function add($mdata)
    {
        try {

            // Insert batch data ke database
            $signal = $this->db->table("member_sinyal");

            if (!$signal->insertBatch($mdata)) {
                return (object) [
                    "code"    => 500,
                    "message" => "Failed to insert signal"
                ];
            }

            return (object) [
                'code'    => 201,
                'message' => 'Order has been succesfully created.',
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred.'
            ];
        }
    }

    public function getBalance_btc($id_member) {
        try {
            $sql = "SELECT
                        COALESCE(SUM(amount_btc), 0) as balance
                    FROM
                        member_sinyal ms
                        INNER JOIN sinyal ON sinyal.id = ms.sinyal_id
                    WHERE
                        sinyal.status = 'filled'  -- Hanya order yang sudah dieksekusi
                        AND sinyal.is_deleted = 'no'  -- Mengabaikan order yang dibatalkan
                        AND sinyal.pair_id IS NULL  -- Mengabaikan transaksi sell
                        AND ms.member_id = ?";
            $query = $this->db->query($sql, $id_member)->getRow();

            return (object) [
                'code' => 200,
                'message' => $query->balance
            ];

        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred.'
            ];
        }
    }
    
}
