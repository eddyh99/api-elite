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
                        ms.order_id
                    FROM
                        sinyal s
                        INNER JOIN member_sinyal ms ON ms.sinyal_id = s.id
                        INNER JOIN member m ON m.id = ms.member_id
                    WHERE
                        s.id = ? AND s.status = 'new' AND s.is_deleted = 'no'";
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

    public function get_buy_order_pending()
    {
        try {
            $sql = "SELECT
                        sinyal.type,
                        ms.member_id,
                        ms.order_id
                    FROM
                        sinyal
                        INNER JOIN member_sinyal ms ON ms.sinyal_id = sinyal.id
                        INNER JOIN member m ON m.id = ms.member_id
                    WHERE
                        sinyal.status = 'pending'
                        AND is_deleted = 'no'
                        AND sinyal.type LIKE 'BUY%'";
            $query = $this->db->query($sql)->getResult();

            if (!$query) {
                return (object) [
                    'code' => 400,
                    'message' => 'No pending buy orders found!'
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
            'message' => $query
        ];
    }

    public function get_sell_order_pending()
    {
        try {
            $sql = "SELECT
                        sinyal.id, 
                        sinyal.type,
                        ms.member_id,
                        ms.order_id
                    FROM sinyal
                    INNER JOIN member_sinyal ms ON ms.sinyal_id = sinyal.id
                    INNER JOIN member m ON m.id = ms.member_id
                    WHERE 
                        sinyal.status = 'pending'
                        AND sinyal.is_deleted = 'no'
                        AND sinyal.type IN ('Sell A', 'Sell B', 'Sell C', 'Sell D')
                    GROUP BY sinyal.type
                    ";
            $query = $this->db->query($sql)->getResult();

            if (!$query) {
                return (object) [
                    'code' => 400,
                    'message' => 'No pending sell orders found!'
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
            'message' => $query
        ];
    }

    public function update_status_order($mdata)
    {

        try {
            // Start Transaction
            $this->db->transBegin();
    
            // Table initialization
            $member_signal = $this->db->table("member_sinyal");
    
            // Jika lebih dari 1 data, gunakan updateBatch()
            if(!empty($mdata['orders'])) {
                if (count($mdata['orders']) > 1) {
                    if (!$member_signal->updateBatch($mdata['orders'], 'order_id')) {
                        $this->db->transRollback();
                        return (object) [
                            "code"    => 400,
                            "message" => "Failed to update orders"
                        ];
                    }
                } else {
                    // Jika hanya 1 data, gunakan update()
                    $order = $mdata['orders'][0];
                    if (!$member_signal->where('order_id', $order['order_id'])->update($order)) {
                        $this->db->transRollback();
                        return (object) [
                            "code"    => 400,
                            "message" => "Failed to update order"
                        ];
                    }
                }
            }
    
            // Pastikan order_ids tidak kosong sebelum diproses
            $order_ids = array_map('intval', $mdata['order_ids']);
            if (empty($order_ids)) {
                $this->db->transRollback();
                return (object) [
                    "code"    => 400,
                    "message" => "No order IDs provided"
                ];
            }
    
            // Update status di tabel sinyal menggunakan Query Builder
            $this->db->table('sinyal')
                ->whereIn('id', function ($builder) use ($order_ids) {
                    $builder->select('sinyal_id')
                            ->from('member_sinyal')
                            ->whereIn('order_id', $order_ids);
                })
                ->update(['status' => 'FILLED']);
    
            // Commit transaksi
            $this->db->transCommit();
    
            return (object) [
                "code"    => 201,
                "message" => "Success"
            ];
        } catch (\Exception $e) {
            // Rollback jika ada error
            $this->db->transRollback();
    
            // Log error untuk debugging
            log_message('error', $e->getMessage());
    
            return (object) [
                "code"    => 500,
                "message" => "An internal server error occurred"
            ];
        }
    }

    public function get_all()
    {
        try {
            $sql = "SELECT
                        s.id,
                        s.status,
                        s.type,
                        s.entry_price,
                        COALESCE(mr.alias, 'unknown') AS admin,
                        DATE(s.created_at) AS date,
                        TIME(s.created_at) AS time
                    FROM
                        sinyal s
                        LEFT JOIN member_role mr ON mr.member_id = s.admin_id
                    ORDER BY date DESC, time DESC";
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
            if ($this->db->affectedRows() === 0) {
                return (object) [
                    'code' => 400,
                    'message' => 'Failed.'
                ];
            }
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
