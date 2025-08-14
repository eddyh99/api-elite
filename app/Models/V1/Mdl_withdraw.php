<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;

/*----------------------------------------------------------
    Modul Name  : Database Withdraw
    Desc        : null
    Sub fungsi  : null

------------------------------------------------------------*/


class Mdl_withdraw extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'withdraw';
    protected $primaryKey = 'id';

    protected $allowedFields = ['member_id', 'withdraw_type', 'amount', 'jenis', 'payment_details', 'wallet_address', 'status', 'admin_notes', 'ref_id','is_topup'];

    protected $returnType = 'object';
    protected $useTimestamps = false;


    public function insert_withdraw($mdata)
    {
        try {
            // $withdraw = $mdata[0]; // Assuming single insert per call
            // $memberId = $withdraw['member_id'];
            // $amount = (float)($withdraw['amount'] ?? 0);
            // $jenis = $withdraw['jenis'] ?? null;
    
            // if ($jenis === 'trade' && $amount > 0) {
            //     // Fetch unmatched Buy signals for the member
            //     $sql = "SELECT COUNT(*) as open_count
            //         FROM member_sinyal ms
            //         JOIN sinyal s ON s.id = ms.sinyal_id
            //         WHERE ms.member_id = ?
            //           AND s.status IN ('pending', 'filled')
            //           AND s.type LIKE 'Buy%'
            //           AND NOT EXISTS (
            //               SELECT 1
            //               FROM sinyal s2
            //               WHERE s2.type LIKE 'Sell%'
            //                 AND s2.pair_id = s.pair_id
            //                 AND s2.status = 'filled'
            //           )";
            //     $result = $this->db->query($sql, [$memberId])->getRowArray();
            //     $openCount = (int)($result['open_count'] ?? 0);
    
            //     // Determine divisor based on openCount
            //     $divisor = 0;
            //     if ($openCount === 1) {
            //         $divisor = 3;
            //     } elseif ($openCount === 2) {
            //         $divisor = 2;
            //     } elseif ($openCount === 3) {
            //         $divisor = 1;
            //     }
    
            //     if ($divisor > 0) {
            //         $newAmount = $amount / $divisor;
            //         // Update position
            //         $this->db->query("UPDATE member SET position = position + ? WHERE id = ?", [$newAmount, $memberId]);
            //     }
            // }
    
            // Insert withdrawal
            $query = $this->insertBatch($mdata);
    
            if (!$query) {
                return (object) [
                    'code'    => 400,
                    'message' => 'Withdrawal request failed.'
                ];
            }
    
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'Withdrawal request failed. Please try again later.' .$e
            ];
        }

            
        return (object) [
            'code'    => 201,
            'id'      =>  $this->db->insertID(),
            'message' => 'Withdrawal request submitted successfully.'
        ];
    }

    public function list_withdraw() {
        try {
            $sql = "SELECT
                        w.id,
                        m.email,
                        w.requested_at,
                        w.amount,
                        w.withdraw_type
                    FROM
                        withdraw w
                        INNER JOIN member m ON m.id = w.member_id
                    WHERE jenis='withdraw' and w.status='pending'
                    ";
            $query = $this->db->query($sql)->getResult();
    
            if (!$query) {
                return (object) [
                    'code'    => 404,
                    'message' => []
                ];
            }
        } catch (\Throwable $th) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while retrieving withdrawal records. Please try again later.'
            ];
        }
    
        return (object) [
            'code'    => 200,
            'message' => 'Withdrawal records retrieved successfully.',
            'data'    => $query
        ];
    }

    public function update_status($mdata) {

        try {
            $query = $this->where('member_id', $mdata['member_id'])
              ->where('id', $mdata['id'])
              ->set($mdata['data'])
              ->update();
    
            if (!$query) {
                return (object) [
                    'code'    => 400,
                    'message' => 'Failed to update withdrawal status.'
                ];
            }
            
            if ($this->db->affectedRows() === 0) {
                return (object) [
                    'code'    => 404,
                    'message' => 'No withdrawal request found for this user.'
                ];
            }

            // destroy fee data
            if($mdata['data']['status'] == 'rejected') {
                $this->where('ref_id', $mdata['id'])->delete();
            }
    
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => $e->getMessage()
            ];
        }
    
        return (object) [
            'code'    => 201,
            'message' => 'Withdrawal status has been ' .$mdata['data']['status']
        ];
    }

    // public function getAvailable_commission($member_id) {
    //     try {

    //         $sql = "SELECT
    //                     COALESCE(SUM(md.commission), 0) - COALESCE(w.amount, 0) AS balance
    //                 FROM
    //                     member
    //                     INNER JOIN member_deposit md ON md.member_id = member.id
    //                     LEFT JOIN (
    //                         SELECT
    //                             member_id,
    //                             SUM(amount) AS amount
    //                         FROM
    //                             withdraw
    //                         GROUP BY
    //                             member_id
    //                     ) w ON w.member_id = member.id_referral
    //                 WHERE
    //                     member.id_referral = ?";
    //     $query = $this->db->query($sql, [$member_id])->getRow();
    //     if (!$query) {
    //         return (object) [
    //             'code'    => 404,
    //             'message' => 'No commission data found for this member.',
    //             'data'    => $query
    //         ];
    //     }

    //     } catch (\Throwable $th) {
    //         return (object) [
    //             'code'    => 500,
    //             'message' => 'An error occurred'
    //         ];
    //     }

    //     return (object) [
    //         "code"    => 200,
    //         "message"    => "Commission data retrieved successfully.",
    //         "data"    => $query
    //     ];
    // }

    public function get_downline($member_id = NULL)
    {
        try {
            if ($member_id === NULL) {
                $sql = "SELECT
                            COALESCE(COUNT(1), 0) AS downline
                        FROM
                            member
                        WHERE id_referral IS NULL
                        AND status IN ('active', 'referral')
                        AND role IN ('member', 'referral')
                        AND is_delete = FALSE";
    
                $query = $this->db->query($sql)->getRow();
            } else {
                $sql = "SELECT
                            COALESCE(COUNT(1), 0) AS downline
                        FROM
                            member
                        WHERE id_referral = ?
                        AND status IN ('active', 'referral')
                        AND is_delete = FALSE";
    
                $query = $this->db->query($sql, [$member_id])->getRow();
            }

            if (!$query) {
                return (object) [
                    'code'    => 200,
                    'message' => 'No downline data found',
                    'data'    => $query
                ];
            }
        } catch (Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred'
            ];
        }
        return (object) [
            "code"    => 200,
            "message"    => "Downline data retrieved successfully",
            "data"    => $query
        ];
    }

    public function history($member_id)
    {
        try {
            $sql = "SELECT 
                        w.requested_at as date,
                        w.amount,
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
                    GROUP BY 
                        w.jenis, w.status";

            $query = $this->db->query($sql, [$member_id])->getResult();

            if (!$query) {
                return (object) [
                    'code'    => 200,
                    'message' => 'Withdraw history not found',
                    'data'    => []
                ];
            }
        } catch (Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred'
            ];
        }
        return (object) [
            "code"    => 200,
            "message"    => 'Withdraw history retrieved successfully',
            "data"    => $query
        ];
    }

    public function history_payment($member_id)
    {
        try {
            $sql = "SELECT 
                        w.requested_at as date,
                        w.amount,
                        CONCAT(w.jenis,' ',withdraw_type) AS description,
                        w.wallet_address,
                        w.status,
                        CONCAT('withdraw', ' ' ,w.withdraw_type) as type
                    FROM 
                        withdraw w
                    WHERE 
                        w.member_id = ?
                        AND w.status <> 'rejected' AND w.jenis = 'withdraw'";

            $query = $this->db->query($sql, [$member_id])->getResult();

            if (!$query) {
                return (object) [
                    'code'    => 200,
                    'message' => 'Withdraw history not found',
                    'data'    => []
                ];
            }
        } catch (Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred'
            ];
        }
        return (object) [
            "code"    => 200,
            "message"    => 'Withdraw history retrieved successfully',
            "data"    => $query
        ];
    }

    
    public function getDetail_withdraw($id)
    {
        try {
            $sql = "SELECT mb.email, wd.requested_at, wd.member_id, wd.withdraw_type, wd.amount, wd.wallet_address, wd.payment_details, wd.status 
                    FROM withdraw wd 
                        INNER JOIN member mb ON wd.member_id=mb.id 
                    WHERE wd.id=?";

            $query = $this->db->query($sql, [$id])->getRow();

            if (!$query) {
                return (object) [
                    'code'    => 200,
                    'message' => 'No withdrawal data found',
                    'data'    => []
                ];
            }
        } catch (Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred'
            ];
        }

        return (object) [
            "code"    => 200,
            "message"    => "Withdrawal data retrieved successfully",
            "data"    => [
                'withdraw_type'     => $query->withdraw_type ?? null,
                'wallet_address'    => $query->wallet_address ?? null,
                'payment_details'   => json_decode($query->payment_details),
                'status'            => $query->status ?? 'pending',
                "amount"            => $query->amount,
                "email"             => $query->email,
                "member_id"         => $query->member_id,
                "requested_at"      => $query->requested_at
            ]
        ];
    }
    
    
}
