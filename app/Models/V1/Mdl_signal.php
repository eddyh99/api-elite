<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;

/*----------------------------------------------------------
    Modul Name  : Database Signal
    Desc        : null
    Sub fungsi  : null

------------------------------------------------------------*/


class Mdl_signal extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'sinyal';
    protected $primaryKey = 'id';

    protected $allowedFields = ['type', 'entry_price', 'pair_id', 'is_deleted', 'status'];

    protected $returnType = 'array';
    protected $useTimestamps = true;

    public function get_latest_signals()
    {
        try {
            $sql = "SELECT
                        buy.id AS id,
                        buy.type AS type,
                        buy.entry_price AS entry_price,
                        buy.status AS status,
                        sell.id AS sell_id,
                        sell.type AS sell_type,
                        sell.entry_price AS sell_entry_price,
                        sell.status AS sell_status
                    FROM
                        sinyal AS buy
                    LEFT JOIN
                        sinyal AS sell
                        ON sell.pair_id = buy.id
                        AND sell.type LIKE 'Sell%'
                        AND sell.status = 'pending'
                        AND sell.is_deleted = 'no'
                    WHERE
                        buy.type LIKE 'Buy%'
                        AND buy.status != 'canceled'
                        AND buy.pair_id IS NULL
                        AND buy.is_deleted = 'no'";

            $query = $this->db->query($sql)->getResult();

            return (object) [
                'code' => 200,
                'message' => $query
            ];

        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred'
            ];
        }
    }

    public function add($mdata, $sell = false)
    {
        try {

            // Insert batch data ke database
            $signal = $this->db->table("sinyal");

            if (!$signal->insert($mdata)) {
                return (object) [
                    "code"    => 500,
                    "message" => "Failed to insert signal"
                ];
            }

            $id = $this->db->insertID();

            if ($sell) {
                $pair_id = $mdata['pair_id'];
                $signal->where('id', $pair_id)->update(['pair_id' => $pair_id]);
            }            

            return (object) [
                'code'    => 201,
                'message' => 'Signal has been successfully added.',
                'id'      => $id
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred.'
            ];
        }
    }

    public function fill_order($id) {
        try {
            // Update order status to "filled"
            $this->set(['status' => 'filled'])
                 ->where('id', $id)
                 ->update();

            if ($this->affectedRows() === 0) {
                return (object) [
                    'code'    => 404,
                    'message' => 'No order was updated.'
                ];
            }
    
            return (object) [
                'code'    => 201,
                'message' => 'Order status has been filled.'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while updating the order status.'
            ];
        }
    }

    public function get_orders($id_signal)
    {
        try {
            $sql = "SELECT
                        ms.member_id,
                        s.order_id,
                        ms.amount_usdt,
                        m.id_referral AS upline,
                        (
                            SELECT COALESCE(SUM(ms2.amount_usdt), 0)
                            FROM member_sinyal ms2
                            WHERE ms2.sinyal_id = s.id
                        ) AS total_usdt
                    FROM
                        sinyal s
                        INNER JOIN member_sinyal ms ON ms.sinyal_id = s.id
                        INNER JOIN member m ON m.id = ms.member_id
                    WHERE
                        s.id = ?
                        AND s.is_deleted = 'no'";
            $query = $this->db->query($sql, [$id_signal])->getResult();

            if (!$query) {
                return (object) [
                    'code' => 404,
                    'message' => 'Orders not found.'
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

    public function get_order($id_signal)
    {
        try {
            // Query to get the signal order by ID
            $sql = "SELECT * FROM sinyal WHERE id = ?";
            $query = $this->db->query($sql, [$id_signal])->getRow();
    
            // If the order is not found
            if (!$query) {
                return (object) [
                    'code' => 404,
                    'message' => 'Order not found.'
                ];
            }
    
            // If the order is not in pending status
            if ($query->status !== 'pending') {
                return (object) [
                    'code' => 400,
                    'message' => 'Only pending orders can be cancelled.'
                ];
            }
    
        } catch (\Exception $e) {
            // General error handling
            return (object) [
                'code' => 500,
                'message' => 'An unexpected error occurred.'
            ];
        }
    
        // Success response with order data
        return (object) [
            'code' => 200,
            'message' => $query
        ];
    }
    

    public function getBuy_pending()
    {
        try {
            $sql = "SELECT
                        sinyal.type,
                        sinyal.order_id
                    FROM
                        sinyal
                    WHERE
                        sinyal.status = 'pending'
                        AND is_deleted = 'no'
                        AND sinyal.type LIKE 'BUY%'";
            $query = $this->db->query($sql)->getRow();
            
            return (object) [
                'code' => 200,
                'message' => $query
            ];

        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred'
            ];
        }

    }

    public function getSell_pending()
    {
        try {
            $sql = "SELECT
                        sinyal.id,
                        sinyal.pair_id,
                        sinyal.order_id
                    FROM
                        sinyal
                    WHERE
                        sinyal.status = 'pending'
                        AND sinyal.is_deleted = 'no'
                        AND sinyal.type IN ('Sell A', 'Sell B', 'Sell C', 'Sell D')
                    GROUP BY
                        sinyal.type
                    ";
            $query = $this->db->query($sql)->getResult();

            return (object) [
                'code' => 200,
                'message' => $query
            ];

        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred'
            ];
        }


    }

    public function updateStatus_byOrder($mdata)
    {
        try {

            // Table initialization
            $signal = $this->db->table("sinyal");

            // Update batch berdasarkan order_id
            $signal->updateBatch($mdata['order'], 'order_id');

            if(!empty($mdata['pair_id'])) {
                $signal->updateBatch($mdata['pair_id'], 'id');
            }

            return (object) [
                "code"    => 201,
                "message" => "Orders updated successfully"
            ];

        } catch (\Exception $e) {
    
            return (object) [
                "code"    => 500,
                "message" => "An internal server error occurred",
                "error"   => $e 
            ];
        }
    }
    
    public function getBtc_bySignal($id_signal)
    {
        try {
            $sql = "SELECT
                        COALESCE( sum(ms.amount_btc), 0) as btc,
                        status
                    FROM
                        sinyal
                        INNER JOIN member_sinyal ms ON ms.sinyal_id = sinyal.id
                    where
                        sinyal.id = ?";
            $query = $this->db->query($sql, $id_signal)->getRow();

            if (is_null($query->status)) {
                return (object) [
                    'code' => 404,
                    'message' => 'Sinyal not found.'
                ];
            }

        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred'
            ];
        }

        return (object) [
            'code' => 200,
            'message' => $query,
            'btc' => $query->btc
        ];


    }

    public function get_all()
    {
        try {
            $sql = "SELECT
                        s.id,
                        s.order_id,
                        s.status,
                        s.type,
                        s.entry_price,
                        '-' as admin,
                        DATE(s.created_at) AS date,
                        TIME(s.created_at) AS time
                    FROM
                        sinyal s
                    ORDER BY
                        date DESC,
                        time Desc";
            $query = $this->db->query($sql)->getResult();

            if (!$query) {
                return (object) [
                    'code' => 200,
                    'message' => []
                ];
            }
        } catch (\Throwable $th) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred'
            ];
        }

        return (object) [
            'code' => 200,
            'message' => $query
        ];
    }

    public function destroy($id_signal) {
        try {
            // Update status dan is_deleted di tabel signals

            $signal = $this->db->table('sinyal')->where('id', $id_signal)->get()->getRow();

            if (!$signal) {
                return (object) ['code' => 404, 'message' => 'Signal not found.'];
            }

            $this->db->table('sinyal')->where('id', $id_signal)->update([
                'status' => 'canceled',
                'is_deleted' => 'yes'
            ]);
            
            if (str_starts_with(strtolower($signal->type), 'sell') && $signal->pair_id) {
                $this->db->table('sinyal')->where('id', $signal->pair_id)
                ->update(['pair_id' => NULL]);
            }            
    
            // Periksa apakah ada baris yang terpengaruh
            // if ($this->db->affectedRows() === 0) {
            //     return (object) [
            //         'code' => 400,
            //         'message' => 'Failed.'
            //     ];
            // }
        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    
        return (object) [
            'code' => 201,
            'message' => 'Signal has been deleted.'
        ];
    }

    public function truncate()
    {
        try {
            // Cek apakah ada order dengan status "new"
            $query = $this->db->table('sinyal')->where('status', 'pending')->countAllResults();

            if ($query > 0) {
                return (object) [
                    'code' => 400,
                    'message' => "There are pending orders. Cancel them first."
                ];
            }


            // Truncate tabel
            $this->db->query("SET FOREIGN_KEY_CHECKS=0;");

            // Truncate tabel
            $this->db->query("TRUNCATE TABLE sinyal;");
            $this->db->query("TRUNCATE TABLE member_sinyal;");

            // Aktifkan kembali foreign key check
            $this->db->query("SET FOREIGN_KEY_CHECKS=1;");

            return (object) [
                'code' => 201,
                'message' => "Tables truncated successfully."
            ];
        } catch (\Exception $e) {
            return (object) [
                'code' => 500,
                'message' => "An error occurred"
            ];
        }
    }
    
}
