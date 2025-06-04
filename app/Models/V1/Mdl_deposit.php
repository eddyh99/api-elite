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

    // public function get_amount()
    // {
    //     try {
    //         $sql = "SELECT
    //                     COALESCE(SUM(amount), 0) / 4 AS amount
    //                 FROM
    //                     member_deposit
    //                 WHERE
    //                     status = 'complete';
    //                 ";
    //         $query = $this->db->query($sql)->getRow();

    //         return (object) [
    //             'code' => 200,
    //             'message' => $query->amount
    //         ];

    //     } catch (\Exception $e) {
    //         return (object) [
    //             'code' => 500,
    //             'message' => 'An error occurred.' .$e
    //         ];
    //     }
    // }

    public function getTotal_tradeBalance() {
        try {
            $sql = "SELECT
                        COALESCE((
                            SELECT SUM(client_wallet)
                            FROM wallet
                        ), 0) -- wallet

                        + COALESCE((
                            SELECT SUM(amount)
                            FROM withdraw
                            WHERE jenis = 'trade'
                        ) 
                        - COALESCE((
                            SELECT SUM(amount)
                            FROM withdraw
                            WHERE jenis = 'balance' AND withdraw_type = 'fiat'
                        ), 0) , 0) -- trade
                            AS amount";
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
    public function getMember_tradeBalance() {
        try {
            // $sql = "SELECT 
            //             m.id AS member_id,
            //             COALESCE(w.client_wallet, 0) 
            //             + COALESCE(wd_trade.total_trade_withdraw, 0)
            //             - COALESCE(wd_fiat.total_fiat_withdraw, 0) AS trade_balance
            //         FROM member m

            //         LEFT JOIN wallet w ON w.member_id = m.id

            //         LEFT JOIN (
            //             SELECT member_id, SUM(amount) AS total_trade_withdraw
            //             FROM withdraw
            //             WHERE jenis = 'trade'
            //             GROUP BY member_id
            //         ) wd_trade ON wd_trade.member_id = m.id

            //         LEFT JOIN (
            //             SELECT member_id, SUM(amount) AS total_fiat_withdraw
            //             FROM withdraw
            //             WHERE jenis = 'balance' AND withdraw_type = 'fiat'
            //             GROUP BY member_id
            //         ) wd_fiat ON wd_fiat.member_id = m.id

            //         HAVING trade_balance > 0";
            $sql = "SELECT
                    m.id as member_id,
                    FLOOR(
                        (
                            COALESCE(
                                (
                                    SELECT
                                        - SUM(master_wallet)
                                    FROM
                                        wallet
                                    WHERE
                                        member_id = m.id
                                ),
                                0
                            ) - COALESCE(
                                (
                                    SELECT
                                        SUM(
                                            CASE
                                                WHEN s.type LIKE 'Buy%' THEN ms.amount_usdt
                                            END
                                        )
                                    FROM
                                        member_sinyal ms
                                        JOIN sinyal s ON s.id = ms.sinyal_id
                                    WHERE
                                        ms.member_id = m.id
                                        AND s.status != 'canceled'
                                ),
                                0
                            ) + COALESCE(
                                (
                                    SELECT
                                        SUM(
                                            CASE
                                                WHEN s.type LIKE 'Sell%' THEN ms.amount_usdt
                                            END
                                        )
                                    FROM
                                        member_sinyal ms
                                        JOIN sinyal s ON s.id = ms.sinyal_id
                                    WHERE
                                        ms.member_id = m.id
                                        AND s.status = 'filled'
                                ),
                                0
                            ) + COALESCE(
                                (
                                    SELECT
                                        SUM(amount)
                                    FROM
                                        withdraw
                                    WHERE
                                        member_id = m.id
                                        AND jenis = 'trade'
                                ),
                                0
                            ) - COALESCE(
                                (
                                    SELECT
                                        SUM(amount)
                                    FROM
                                        withdraw
                                    WHERE
                                        member_id = m.id
                                        AND jenis = 'balance'
                                        AND withdraw_type = 'usdt'
                                ),
                                0
                            )
                        ) * 100
                    ) / 100 AS trade_balance
                FROM
                    member m
                HAVING
                    trade_balance >= 10";
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
                      -- USDT balance: deposits + balance withdraws - real withdraws/trades
                      COALESCE((
                        SELECT SUM(amount)
                        FROM member_deposit
                        WHERE status = 'complete' AND member_id = ?
                      ), 0)
                      +
                      COALESCE((
                        SELECT SUM(amount)
                        FROM withdraw
                        WHERE member_id = ? AND (jenis = 'balance' or jenis='comission') AND withdraw_type = 'usdt'
                      ), 0)
                      -
                      COALESCE((
                        SELECT SUM(amount)
                        FROM withdraw
                        WHERE member_id = ?
                          AND (
                            (jenis = 'withdraw' AND status <> 'rejected' AND (withdraw_type = 'usdt' or withdraw_type = 'usdc'))
                            OR (jenis = 'trade' AND withdraw_type = 'usdt')
                          )
                      ), 0) AS usdt,
                    
                      -- BTC balance: balance - trade - actual withdrawn
                      COALESCE((
                        SELECT SUM(x.amount)
                        FROM withdraw x
                        WHERE x.member_id = ?
                          AND x.jenis = 'balance'
                          AND x.withdraw_type = 'btc'
                      ), 0)
                      -
                      COALESCE((
                        SELECT SUM(y.amount)
                        FROM withdraw y
                        WHERE y.member_id = ?
                          AND y.jenis = 'trade'
                          AND y.withdraw_type = 'btc'
                      ), 0)
                      -
                      COALESCE((
                        SELECT SUM(z.amount)
                        FROM withdraw z
                        WHERE z.member_id = ?
                          AND (
                            (z.jenis = 'withdraw' AND z.status <> 'rejected' AND z.withdraw_type = 'btc')
                            OR (z.jenis = 'trade' AND z.withdraw_type = 'btc')
                          )
                      ), 0) AS btc;"; 
            $query = $this->db->query($sql, [$id_member, $id_member, $id_member,$id_member, $id_member, $id_member])->getRow();

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
