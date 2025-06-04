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

    public function store($data)
    {
        try {
            $builder = $this->db->table("settings");
            $key = key($data);
            $value = $data[$key];
    
            $exists = $builder->where('key', $key)->get()->getRow();
    
            if ($exists) {
                $builder->where('key', $key)->update(['value' => $value]);
            } else {
                $builder->insert(['key' => $key, 'value' => $value]);
            }

            return (object) [
                'success'  => true,
                'message' => ''
            ];
        } catch (\Exception $e) {
            return (object) [
                'success'  => false,
                'code'    => $e->getCode(),
                'message' => 'An error occurred.' . $e
            ];
        }
    }
}
