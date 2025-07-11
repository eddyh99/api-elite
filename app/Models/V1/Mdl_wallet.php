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
            // old query
            // $sql = "SELECT 
            //             FLOOR((
            //                 COALESCE((
            //                     SELECT -SUM(master_wallet)
            //                     FROM wallet
            //                     WHERE member_id = ?
            //                 ), 0)
            //                 - COALESCE((
            //                   SELECT SUM(amount) 
            //                   FROM member_commission
            //                   WHERE downline_id=?
            //                 ),0)
            //                 - COALESCE((
            //                     SELECT SUM(
            //                         CASE
            //                             WHEN s.type LIKE 'Buy%' THEN ms.amount_usdt
            //                         END
            //                     )
            //                     FROM member_sinyal ms
            //                     JOIN sinyal s ON s.id = ms.sinyal_id
            //                     WHERE ms.member_id = ?
            //                     AND s.status != 'canceled'
            //                 ), 0)
            //                 + COALESCE((
            //                     SELECT SUM(
            //                         CASE
            //                             WHEN s.type LIKE 'Sell%' THEN ms.amount_usdt
            //                         END
            //                     )
            //                     FROM member_sinyal ms
            //                     JOIN sinyal s ON s.id = ms.sinyal_id
            //                     WHERE ms.member_id = ?
            //                     AND s.status = 'filled'
            //                 ), 0)
            //                 + COALESCE((
            //                     SELECT SUM(amount)
            //                     FROM withdraw
            //                     WHERE member_id = ?
            //                     AND jenis = 'trade'
            //                 ), 0)
            //                 - COALESCE((
            //                     SELECT SUM(amount)
            //                     FROM withdraw
            //                     WHERE member_id = ?
            //                     AND jenis = 'balance'
            //                     AND withdraw_type = 'usdt'
            //                 ), 0)
            //             ) * 100) / 100 AS usdt,
            //           COALESCE(
            //             (SELECT SUM(
            //                CASE
            //                  WHEN s.type LIKE 'Buy%'  THEN ms.amount_btc
            //                  WHEN s.type LIKE 'Sell%' THEN -ms.amount_btc
            //                  ELSE 0
            //                END
            //              )
            //              FROM member_sinyal ms
            //              JOIN sinyal s  ON s.id = ms.sinyal_id
            //              WHERE ms.member_id = ?
            //              AND s.status='filled'
            //             ), 0
            //           )
            //           + COALESCE(
            //             (SELECT SUM(x.amount)
            //              FROM withdraw x
            //              WHERE x.member_id     = ?
            //                AND x.jenis         = 'trade'
            //                AND x.withdraw_type = 'btc'
            //             ), 0
            //           )
            //           - COALESCE(
            //             (SELECT SUM(y.amount)
            //              FROM withdraw y
            //              WHERE y.member_id     = ?
            //                AND y.jenis         = 'balance'
            //                AND y.withdraw_type = 'btc'
            //             ), 0
            //           )
            //           AS btc;"; 

            $sql = "SELECT
                    -- id superadmin
                    CASE WHEN m.role = 'superadmin' and m.id = 1
                    THEN
                    FLOOR(
                        (
                        /* 1) all master_wallet (global) */
                        COALESCE((SELECT SUM(master_wallet) FROM wallet), 0)
                        /* 2) + this member’s client_wallet */
                        + COALESCE((SELECT SUM(client_wallet)
                                    FROM wallet
                                    WHERE member_id = m.id), 0)
                        /* 3) − unclosed Buys for this member */
                        - COALESCE(
                            (
                                SELECT SUM(ms.amount_usdt)
                                FROM member_sinyal ms
                                JOIN sinyal s ON s.id = ms.sinyal_id
                                WHERE
                                ms.member_id = m.id
                                AND s.type   LIKE 'Buy%'
                                AND s.status = 'filled'
                                /* exclude any Buy whose pair_id has already been Sold by this member */
                                AND NOT EXISTS (
                                    SELECT 1
                                    FROM member_sinyal ms2
                                    JOIN sinyal     s2 ON s2.id = ms2.sinyal_id
                                    WHERE
                                    ms2.member_id = ms.member_id
                                    AND s2.type    LIKE 'Sell%'
                                    AND s2.status  = 'filled'
                                    AND s2.pair_id = s.pair_id
                                )
                            ),
                            0
                            )
                        /* 4) + this member’s trade-withdrawals (USDT) */
                        + COALESCE(
                            (SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id   = m.id
                                AND jenis       = 'trade'
                                AND withdraw_type = 'usdt'),
                            0
                            )
                        /* 5) − this member’s balance-withdrawals (USDT) */
                        - COALESCE(
                            (SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id   = m.id
                                AND jenis       = 'balance'
                                AND withdraw_type = 'usdt'),
                            0
                            )
                        ) * 100
                    ) / 100
                    ELSE -- else superadmin
                    FLOOR((
                        COALESCE((
                            SELECT -SUM(master_wallet)
                            FROM wallet
                            WHERE member_id = m.id
                        ), 0)
                        
                        - COALESCE((
                            SELECT SUM(amount)
                            FROM member_commission
                            WHERE downline_id = m.id
                        ), 0)
                        
                        - COALESCE((
                            SELECT SUM(
                                CASE
                                    WHEN s.type LIKE 'Buy%' THEN ms.amount_usdt
                                END
                            )
                            FROM member_sinyal ms
                            JOIN sinyal s ON s.id = ms.sinyal_id
                            WHERE ms.member_id = m.id
                            AND s.status != 'canceled'
                        ), 0)
                        
                        + COALESCE((
                            SELECT SUM(
                                CASE
                                    WHEN s.type LIKE 'Sell%' THEN ms.amount_usdt
                                END
                            )
                            FROM member_sinyal ms
                            JOIN sinyal s ON s.id = ms.sinyal_id
                            WHERE ms.member_id = m.id
                            AND s.status = 'filled'
                        ), 0)
                        
                        + COALESCE((
                            SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id = m.id
                            AND jenis = 'trade'
                        ), 0)
                        
                        - COALESCE((
                            SELECT SUM(amount)
                            FROM withdraw
                            WHERE member_id = m.id
                            AND jenis = 'balance'
                            AND withdraw_type = 'usdt'
                        ), 0)
                    ) * 100) / 100 END AS usdt,

                    COALESCE((
                        SELECT SUM(
                            CASE
                                WHEN s.type LIKE 'Buy%' THEN ms.amount_btc
                                WHEN s.type LIKE 'Sell%' THEN -ms.amount_btc
                                ELSE 0
                            END
                        )
                        FROM member_sinyal ms
                        JOIN sinyal s ON s.id = ms.sinyal_id
                        WHERE ms.member_id = m.id
                        AND s.status = 'filled'
                    ), 0)

                    + COALESCE((
                        SELECT SUM(x.amount)
                        FROM withdraw x
                        WHERE x.member_id = m.id
                        AND x.jenis = 'trade'
                        AND x.withdraw_type = 'btc'
                    ), 0)

                    - COALESCE((
                        SELECT SUM(y.amount)
                        FROM withdraw y
                        WHERE y.member_id = m.id
                        AND y.jenis = 'balance'
                        AND y.withdraw_type = 'btc'
                    ), 0) AS btc

                FROM member m
                WHERE m.id = ?"; 
            $query = $this->db->query($sql, [$id_member])->getRow();

            return (object) [
                'code' => 200,
                'message' => $query
            ];

        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => $e->getMessage()//'An error occurred.'
            ];
        }
    }
    
}
