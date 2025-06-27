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
            // $sql = "SELECT
            //             COALESCE((
            //                 SELECT SUM(client_wallet)
            //                 FROM wallet
            //             ), 0) -- wallet

            //             + COALESCE((
            //                 SELECT SUM(amount)
            //                 FROM withdraw
            //                 WHERE jenis = 'trade'
            //             ) 
            //             - COALESCE((
            //                 SELECT SUM(amount)
            //                 FROM withdraw
            //                 WHERE jenis = 'balance' AND withdraw_type = 'fiat'
            //             ), 0) , 0) -- trade
            //                 AS amount";

            // $sql = "SELECT
            //             SUM(trade_balance) AS amount
            //         FROM (
            //             SELECT
            //                 m.id AS member_id,
            //                 FLOOR((
            //                     COALESCE((
            //                         SELECT -SUM(master_wallet)
            //                         FROM wallet
            //                         WHERE member_id = m.id
            //                     ), 0)
            //                     - COALESCE((
            //                         SELECT SUM(CASE WHEN s.type LIKE 'Buy%' THEN ms.amount_usdt END)
            //                         FROM member_sinyal ms
            //                         JOIN sinyal s ON s.id = ms.sinyal_id
            //                         WHERE ms.member_id = m.id AND s.status != 'canceled'
            //                     ), 0)
            //                     + COALESCE((
            //                         SELECT SUM(CASE WHEN s.type LIKE 'Sell%' THEN ms.amount_usdt END)
            //                         FROM member_sinyal ms
            //                         JOIN sinyal s ON s.id = ms.sinyal_id
            //                         WHERE ms.member_id = m.id AND s.status = 'filled'
            //                     ), 0)
            //                     + COALESCE((
            //                         SELECT SUM(amount)
            //                         FROM withdraw
            //                         WHERE member_id = m.id AND jenis = 'trade'
            //                     ), 0)
            //                     - COALESCE((
            //                         SELECT SUM(amount)
            //                         FROM withdraw
            //                         WHERE member_id = m.id AND jenis = 'balance' AND withdraw_type = 'usdt'
            //                     ), 0)
            //                 ) * 100) / 100 AS trade_balance
            //             FROM
            //                 member m
            //             HAVING
            //                 trade_balance >= 10
            //         ) AS subquery";
            $sql = "SELECT sum(position) as amount FROM member WHERE is_delete=0 AND status='active'";
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
    public function getMember_tradeBalance($member_ids = null) {
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
                    (m.position_a + m.position_b + m.position_c + m.position_d) AS position,
                    m.position_a,
                    m.position_b,
                    m.position_c,
                    m.position_d,
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
                    member m";

            if (!empty($member_ids) && is_array($member_ids)) {
                $ids = implode(',', array_map('intval', $member_ids));
                $sql .= " WHERE m.id IN ($ids)";
            }

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
    
    public function rebalanceMemberPosition($side)
    {
        try {
            // Step 1: Get trade balance
            $result = $this->getMember_tradeBalance();
    
            if ($result->code !== 200) {
                return (object)[
                    'code' => 500,
                    'message' => 'Failed to retrieve trade balances.'
                ];
            }
    
            $totalNewPosition = 0; // Step 2: Running sum of new positions
            $member_ids = [];
            $member_positions = [];
            $side_position = [
                'BUY A' => 'position_a',
                'BUY B' => 'position_b',
                'BUY C' => 'position_c',
                'BUY D' => 'position_d'
            ];

            // step 3 update
            foreach ($result->message as $member) {
            $sql = "SELECT
                        open_count,
                        CASE
                            open_count
                            WHEN 1 THEN 'position_a'
                            WHEN 2 THEN 'position_b'
                            WHEN 3 THEN 'position_c'
                            WHEN 4 THEN 'position_d'
                            ELSE CONCAT('position_', open_count)
                        END AS last_position
                    FROM
                        (
                            SELECT
                                COUNT(*) AS open_count
                            FROM
                                member_sinyal ms
                                JOIN sinyal s ON s.id = ms.sinyal_id
                            WHERE
                                ms.member_id = ?
                                AND s.status IN ('pending', 'filled')
                                AND s.type LIKE 'Buy%'
                                AND NOT EXISTS (
                                    SELECT
                                        1
                                    FROM
                                        sinyal s2
                                    WHERE
                                        s2.type LIKE 'Sell%'
                                        AND s2.pair_id = s.pair_id
                                        AND s2.status = 'filled'
                                )
                    ) AS sub";
            $result = $this->db->query($sql, [$member->member_id])->getRowArray();
            $openCount = (int)($result['open_count'] ?? 0);

            // Determine divisor based on openCount
            $divisor = 0;
            if ($openCount === 1) {
                $divisor = 3;
            } elseif ($openCount === 2) {
                $divisor = 2;
            } elseif ($openCount === 3) {
                $divisor = 1;
            } elseif ($openCount === 0) {
                $divisor = 4;
            }

            $tradeBalance = (float)$member->trade_balance;
            $newPosition = 0;
    
            if ($divisor > 0 && $tradeBalance >= 10) {
                $newPosition = $tradeBalance / $divisor;
    
                if($openCount === 0) { //if first buy
                    // Update position
                    array_push($member_ids, $member->member_id); 
                    $member_positions[] = [
                        'id'                  => $member->member_id,
                        $side_position[$side] => $newPosition
                    ];
                    // $this->db->query("UPDATE member SET {$side_position[$side]} = ? WHERE id = ?", [$newPosition, $member->member_id]);
                } else {
                    //check $newPosition >= prev position
                    $lastPosition = $result['last_position'];
                    if($newPosition >= $member->$lastPosition) {
                        array_push($member_ids, $member->member_id); 
                        $member_positions[] = [
                            'id'                  => $member->member_id,
                            $side_position[$side] => $newPosition
                        ];
                        // $this->db->query("UPDATE member SET {$side_position[$side]} = ? WHERE id = ?", [$newPosition, $member->member_id]);
                    } else {
                        $newPosition = 0;
                    }

                }
            }

            // Accumulate total
            $totalNewPosition += $newPosition;
        }

    
            // Step 4: Return total new position
            return (object)[
                'code' => 200,
                'message' => $totalNewPosition,
                'member_ids' => $member_ids,
                'member_positions' => $member_positions
            ];
        } catch (\Exception $e) {
            return (object)[
                'code' => 500,
                'message' => 'Error in rebalance process. ' . $e->getMessage()
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

            $sql .= "
                    SELECT
                        md.created_at as date,
                        md.amount AS commission,
                        md.amount,
                        CONCAT('deposit') AS description,
                        'deposit' as type,
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

    public function getDeposit_byInvoice($inv) {
        try {
            $sql = "SELECT
                        md.*,
                        m.id_referral
                    FROM
                        member_deposit md
                        INNER JOIN member m ON m.id = md.member_id
                    where
                        md.invoice = ?"; 
            $query = $this->db->query($sql, [$inv])->getRow();

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
