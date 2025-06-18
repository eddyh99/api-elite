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


    // public function add($mdata)
    // {
    //     try {

    //         // Insert batch data ke database
    //         $signal = $this->db->table("member_sinyal");

    //         if (!$signal->insertBatch($mdata)) {
    //             return (object) [
    //                 "code"    => 500,
    //                 "message" => "Failed to insert signal"
    //             ];
    //         }

    //         return (object) [
    //             'code'    => 201,
    //             'message' => 'Order has been succesfully created.',
    //         ];
    //     } catch (\Exception $e) {
    //         return (object) [
    //             'code'    => 500,
    //             'message' => 'An error occurred.'
    //         ];
    //     }
    // }

    public function addOrUpdate($mdata)
{
    try {

        $placeholders = [];
        $bindings = [];

        foreach ($mdata as $index => $row) {
            $placeholders[] = "(?, ?, ?, ?)";
            $bindings[] = $row['member_id'];
            $bindings[] = $row['amount_usdt'];
            $bindings[] = $row['amount_btc'];
            $bindings[] = $row['sinyal_id'];
        }

        $sql = "INSERT INTO member_sinyal (member_id, amount_usdt, amount_btc, sinyal_id) VALUES " .
               implode(', ', $placeholders) .
               " ON DUPLICATE KEY UPDATE 
                    amount_usdt = VALUES(amount_usdt),
                    amount_btc = VALUES(amount_btc)";

        $this->db->query($sql, $bindings);

        return (object)[
            'code'    => 201,
            'message' => 'Data has been inserted or updated successfully.',
        ];

    } catch (\Throwable $e) {
        return (object)[
            'code'    => 500,
            'message' => 'An error occurred: ' . $e->getMessage(),
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

    public function history($id_member) {
        try {
            $sql = "SELECT
                        s.created_at as date,
                        s.entry_price,
                        ms.amount_btc,
                        ms.amount_usdt,
                        SUBSTRING_INDEX(type, ' ', 1) AS position
                    FROM
                        member_sinyal ms
                        INNER JOIN sinyal s ON s.id = ms.sinyal_id
                    WHERE
                        ms.member_id = ?";
            $query = $this->db->query($sql, [$id_member])->getResult();

            if(!$query) {
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

    public function getby_sinyal($id_sinyal)
    {
        $sql = "SELECT
                m.id AS member_id,
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
                
                ms.amount_usdt,
                ms.sinyal_id

            FROM
                member m
            INNER JOIN member_sinyal ms
                ON m.id = ms.member_id AND ms.sinyal_id = ?";

        $query = $this->db->query($sql, [$id_sinyal])->getResult();
        if (!$query) {
            return (object) [
                "code"    => "404",
                "message" => "Data Nor Found"
            ];
        }


        return (object) [
            "code"    => "200",
            "message" => $query
        ];
    }

    
}
