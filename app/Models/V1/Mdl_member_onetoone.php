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
            // Pastikan database connection diakses melalui db property dari model
            $builder = $this->builder(); // Ini bawaan CodeIgniter Model

            $builder->where('is_deleted', 0);
            $result = $builder->get()->getResult();

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
                'message' => $e->getMessage() // biar tahu letak error-nya saat debugging
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
