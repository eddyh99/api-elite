<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

/*----------------------------------------------------------
    Modul Name  : Database Subscription
    Desc        : null
    Sub fungsi  : null

------------------------------------------------------------*/


class Mdl_subscribe extends Model
{
    protected $server_tz = "Asia/Singapore";

    public function subs_add($mdata)
    {
        //fix query
        try {
            $subs = $this->db->table("subscription");

            // Insert data into 'subscription' table
            if (!$subs->insert($mdata)) {
                return (object) array(
                    "code"      => 400,
                    "message"   => "Failed to save subscription data"
                );
            }
        } catch (DatabaseException $e) {
            return (object) array(
                "code"      => 500,
                "message"   => "A database error occurred"
            );
        } catch (\Exception $e) {
            return (object) array(
                "code"      => 500,
                "message"   => "An unexpected server error occurred"
            );
        }
    
        return (object) array(
            "code"      => 201,
            "message"   => $mdata['end_date']
        );
    }

    public function update_status($mdata) {
        try {
            // Update status berdasarkan email member
            $sql = "UPDATE subscription 
                    INNER JOIN member ON member.id = subscription.member_id 
                    SET subscription.status = ? 
                    WHERE member.email = ?";
    
            $this->db->query($sql, [$mdata['status'], $mdata['email']]);
            $affectedRows = $this->db->affectedRows();
    
            // Jika update gagal
            if (!$affectedRows) {
                return (object) array(
                    "code"    => 400,
                    "message" => "Failed to update subscription status"
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
            "message" => "Subscription has been updated successfully"
        );
    }

    public function getMembership_history($member_id)
    {
        try {
            $sql = "SELECT
                        start_date,
                        initial_capital,
                        amount_paid,
                        status
                    FROM
                        subscription
                    WHERE
                        member_id = ?";
            $query = $this->db->query($sql, [$member_id])->getResult();

            if (!$query) {
                return (object) [
                    'code'    => 200,
                    'message' => []
                ];
            }
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred'
            ];
        }

        return (object) [
            "code"    => 200,
            "message" => $query
        ];
    }
    
    
}
