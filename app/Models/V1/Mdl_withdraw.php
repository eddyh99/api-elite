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

    protected $allowedFields = ['member_id', 'withdraw_type', 'amount', 'payment_details', 'wallet_address', 'status', 'admin_notes'];

    protected $returnType = 'object';
    protected $useTimestamps = false;


    public function insert_withdraw($mdata)
    {
        try {
            $query = $this->insert($mdata);
    
            if (!$query) {
                return (object) [
                    'code'    => 400,
                    'message' => 'Withdrawal request failed.'
                ];
            }
    
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'Withdrawal request failed. Please try again later.'
            ];
        }

            
        return (object) [
            'code'    => 201,
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
                        INNER JOIN member m ON m.id = w.member_id";
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
            $query = $this->where('member_id', $mdata['member_id'])->set($mdata['data'])->update();
    
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
    
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while updating withdrawal status. Please try again later.'
            ];
        }
    
        return (object) [
            'code'    => 201,
            'message' => 'Withdrawal status has been ' .$mdata['data']['status']
        ];
    }

    public function getAvailable_commission($member_id) {
        try {

            $sql = "SELECT
                        COALESCE(SUM(s.commission), 0) - COALESCE(w.amount, 0) AS balance
                    FROM
                        member
                        INNER JOIN subscription s ON s.member_id = member.id
                        LEFT JOIN (
                            SELECT
                                member_id,
                                SUM(amount) AS amount
                            FROM
                                withdraw
                            GROUP BY
                                member_id
                        ) w ON w.member_id = member.id_referral
                    WHERE
                        member.id_referral = ?";
        $query = $this->db->query($sql, [$member_id])->getRow();
        if (!$query) {
            return (object) [
                'code'    => 404,
                'message' => 'No commission data found for this member.',
                'data'    => $query
            ];
        }

        } catch (\Throwable $th) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred'
            ];
        }

        return (object) [
            "code"    => 200,
            "message"    => "Commission data retrieved successfully.",
            "data"    => $query
        ];
    }

    public function get_downline($member_id)
    {
        try {
            $sql = "SELECT
                    COALESCE(COUNT(r.id), 0) AS downline
                FROM
                    member m
                    LEFT JOIN member r ON r.id_referral = m.id
                    AND r.status = 'active'
                WHERE
                    m.id = ?";

            $query = $this->db->query($sql, [$member_id])->getRow();

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
                        requested_at,
                        amount,
                        status
                    from
                        withdraw
                    where
                        member_id = ?";

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
            $sql = "SELECT withdraw_type, wallet_address, payment_details FROM withdraw WHERE id=?";

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
                'withdraw_type' => $query->withdraw_type ?? null,
                'wallet_address' => $query->wallet_address ?? null,
                'payment_details' => json_decode($query->payment_details)
            ]
        ];
    }
    
    
}
