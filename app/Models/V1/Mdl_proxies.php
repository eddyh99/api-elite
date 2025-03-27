<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;

/*----------------------------------------------------------
    Modul Name  : Database Proxy
    Desc        : null
    Sub fungsi  : null

------------------------------------------------------------*/


class Mdl_proxies extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'proxies';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useTimestamps = true;

    public function get_all()
    {
        try {
            $sql = "SELECT ip_address, port, username, password FROM proxies";
            $query = $this->db->query($sql)->getResult();

            if (!$query) {
                return (object) [
                    'code' => 404,
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
    
    
}
