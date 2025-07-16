<?php

namespace App\Models\V1;

use CodeIgniter\Model;

class Mdl_payment_onetoone extends Model
{
    protected $table            = 'tb_payment_onetoone';
    protected $primaryKey       = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_member_onetoone',
        'status_invoice',
        'link_invoice',
        'invoice_date',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_member_onetoone' => 'required|integer',
        'status_invoice'     => 'in_list[paid,unpaid]',
        'link_invoice'       => 'required|max_length[255]',
    ];

    public function get_all()
    {
        try {
            // Buat query SQL
            $query = "SELECT * FROM tb_payment_onetoone";

            // Jalankan query
            $result = $this->db->query($query);

            // Ambil data hasil query
            $data = $result->getResult();

            // Cek apakah data kosong
            if (empty($data)) {
                return (object) [
                    'code'    => 404,
                    'message' => 'No payment data found.'
                ];
            }

            // Jika data ditemukan, return data
            return (object) [
                'code'    => 200,
                'message' => 'Payment data retrieved successfully.',
                'data'    => $data
            ];
        } catch (\Exception $e) {
            // Jika ada error
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while retrieving payment data.'
            ];
        }
    }
}
