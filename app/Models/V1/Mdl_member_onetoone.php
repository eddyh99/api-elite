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
}
