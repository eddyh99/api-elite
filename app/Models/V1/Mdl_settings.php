<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;

/*----------------------------------------------------------
    Modul Name  : Database Setting
    Desc        : null
    Sub fungsi  : null

------------------------------------------------------------*/


class Mdl_settings extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'settings';
    protected $primaryKey = 'id';

    protected $allowedFields = ['key', 'value'];

    protected $returnType = 'object';
    protected $useTimestamps = false;


    public function get($key)
    {
        try {
            $data =  $this->where($this->db->escapeIdentifiers('key'), $key)->first()->value ?? null;

            return (object) [
                'code'    => 200,
                'message' => $data
            ];
        } catch (\Throwable $th) {
            return (object) [
                'code'    => 500,
                'message' => 'An error occurred while getting data'
            ];
        }
    }
}
