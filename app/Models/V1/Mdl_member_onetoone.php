<?php

namespace App\Models\V1;

use CodeIgniter\Model;

class Mdl_member_onetoone extends Model
{
    protected $table            = 'tb_member_onetone';
    protected $primaryKey       = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Bisa diganti 'object' kalau lebih suka object
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'email',
        'is_deleted',
        'created_at',
        'updated_at',
    ];

    // Jika ingin created_at dan updated_at otomatis terisi:
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function get_all()
    {
        try {
            $sql = "SELECT 
                m.id,
                m.email,
                m.is_deleted,
                p.status_invoice AS last_status_invoice,
                p.link_invoice AS last_link_invoice,
                p.invoice_date AS last_invoice_date
            FROM tb_member_onetone m
            LEFT JOIN (
                SELECT 
                    p1.id_member_onetoone, 
                    p1.status_invoice, 
                    p1.link_invoice, 
                    p1.invoice_date
                FROM tb_payment_onetoone p1
                INNER JOIN (
                    SELECT 
                        id_member_onetoone, 
                        MAX(invoice_date) AS max_date
                    FROM tb_payment_onetoone
                    GROUP BY id_member_onetoone
                ) p2 
                ON p1.id_member_onetoone = p2.id_member_onetoone 
                AND p1.invoice_date = p2.max_date
            ) p ON m.id = p.id_member_onetoone
            WHERE m.is_deleted = 0";

            // Eksekusi SQL langsung
            $result = $this->db->query($sql)->getResult();

            if (empty($result)) {
                return (object)[
                    'code'    => 404,
                    'message' => 'No data found.'
                ];
            }

            return (object)[
                'code'    => 200,
                'message' => 'Data retrieved successfully.',
                'data'    => $result
            ];
        } catch (\Exception $e) {
            return (object)[
                'code'    => 500,
                'message' => $e->getMessage()
            ];
        }
    }


    public function get_by_id($id)
    {
        try {
            // Cek apakah ID valid
            if (!is_numeric($id)) {
                return (object)[
                    'code'    => 400,
                    'message' => 'Invalid ID format.'
                ];
            }

            $data = $this->find($id);
            if (!$data) {
                return (object)[
                    'code'    => 404,
                    'message' => 'User not found.'
                ];
            }

            // Load model payment
            $paymentModel = new Mdl_payment_onetoone();

            // Ambil semua payment user ini
            $paymentData = $paymentModel
                ->where('id_member_onetoone', $id)
                ->findAll();

            return (object)[
                'code'    => 200,
                'message' => 'Data retrieved successfully.',
                'data'    => $data,
                'payment' => $paymentData
            ];
        } catch (\Exception $e) {
            return (object)[
                'code'    => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function get_by_email($email)
    {
        return $this->where('email', $email)->first();
    }


    public function insert_memberonetoone($mdata)
    {
        try {
            // Insert data ke tabel menggunakan insert() dari Model
            if ($this->insert($mdata) === false) {
                // Jika gagal, ambil error bawaan model
                return (object)[
                    'code'    => 400,
                    'message' => $this->errors() // untuk debugging error validasi
                ];
            }

            return (object)[
                'code'    => 201,
                'message' => 'Data inserted successfully.',
                'id'      => $this->getInsertID()
            ];
        } catch (\Exception $e) {
            return (object)[
                'code'    => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function delete_memberonetoone($id)
    {
        try {
            // Pastikan ID valid
            if (!is_numeric($id)) {
                return (object)[
                    'code'    => 400,
                    'message' => 'Invalid ID format.'
                ];
            }

            // Cek apakah data ada
            $data = $this->find($id);
            if (!$data) {
                return (object)[
                    'code'    => 404,
                    'message' => 'User not found.'
                ];
            }

            // Soft delete: ubah is_deleted menjadi 1
            $updated = $this->update($id, ['is_deleted' => 1]);

            if (!$updated) {
                return (object)[
                    'code'    => 400,
                    'message' => $this->errors() ?? 'Failed to delete data.'
                ];
            }

            return (object)[
                'code'    => 200,
                'message' => 'Data deleted successfully.'
            ];
        } catch (\Exception $e) {
            return (object)[
                'code'    => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
