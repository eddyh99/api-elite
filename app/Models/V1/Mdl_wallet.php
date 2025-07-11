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
                            COALESCE(
                                (
                                    SELECT
                                        SUM(master_wallet)
                                    FROM
                                        wallet
                                ),
                                0
                            ) + 
                            COALESCE(
                                (
                                    SELECT
                                        SUM(client_wallet)
                                    FROM
                                        wallet
                                    WHERE member_id=m.id
                                ),
                                0
                            )
                            -- - COALESCE(
                            --     (
                            --         SELECT
                            --             SUM(
                            --                 CASE
                            --                     WHEN s.type LIKE 'Buy%' THEN ms.amount_usdt
                            --                 END
                            --             )
                            --         FROM
                            --             member_sinyal ms
                            --             JOIN sinyal s ON s.id = ms.sinyal_id
                            --         WHERE
                            --             ms.member_id = m.id
                            --             AND s.status != 'canceled'
                            --     ),
                            --     0
                            -- ) + COALESCE(
                            --     (
                            --         SELECT
                            --             SUM(
                            --                 CASE
                            --                     WHEN s.type LIKE 'Sell%' THEN ms.amount_usdt
                            --                 END
                            --             )
                            --         FROM
                            --             member_sinyal ms
                            --             JOIN sinyal s ON s.id = ms.sinyal_id
                            --         WHERE
                            --             ms.member_id = m.id
                            --             AND s.status = 'filled'
                            --     ),
                            --     0
                            -- ) 
                            + COALESCE(
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
