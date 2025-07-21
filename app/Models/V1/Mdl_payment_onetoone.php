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
        'invoice_number',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_member_onetoone' => 'required|integer',
        'status_invoice'     => 'in_list[paid,unpaid]',
        'invoice_number'     => 'required|max_length[255]|is_unique[tb_payment_onetoone.invoice_number]',
        'link_invoice'       => 'required|max_length[255]',
    ];

    public function get_all()
    {
        try {
            // Query dengan JOIN ke tabel member
            $query = "
            SELECT 
                p.id,
                m.email,
                p.status_invoice,
                p.link_invoice,
                p.invoice_date,
                p.created_at,
                p.updated_at
            FROM tb_payment_onetoone AS p
            LEFT JOIN tb_member_onetone AS m ON p.id_member_onetoone = m.id
        ";

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

    public function insert_payment($data)
    {
        try {
            if ($this->insert($data) === false) {
                return (object) [
                    'code'    => 400,
                    'message' => $this->errors()
                ];
            }
            return (object) [
                'code'    => 201,
                'message' => 'Payment data inserted successfully.'
            ];
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateStatusByInvoiceNumber($invoiceNumber, $status)
    {
        $query = $this->db->query(
            "SELECT id FROM tb_payment_onetoone WHERE invoice_number = ?",
            [$invoiceNumber]
        );

        $row = $query->getRow();

        // Jika tidak ditemukan
        if (!$row) {
            return (object) [
                'code'    => 404,
                'status'  => false,
                'message' => "Invoice with number {$invoiceNumber} not found."
            ];
        }

        // Update status_invoice berdasarkan id
        $this->db->query(
            "UPDATE tb_payment_onetoone SET status_invoice = ? WHERE id = ?",
            [$status, $row->id]
        );

        if ($this->db->affectedRows() > 0) {
            return (object) [
                'code'           => 200,
                'status'         => true,
                'message'        => 'Invoice status updated successfully',
            ];
        } else {
            return (object) [
                'code'    => 200,
                'status'  => true,
                'message' => 'No change made. Status is already up to date.',
            ];
        }

        return (object) [
            'code'    => 500,
            'status'  => false,
            'message' => 'Failed to update invoice status.'
        ];
    }
}
