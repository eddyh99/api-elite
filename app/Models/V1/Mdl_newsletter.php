<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;

/*----------------------------------------------------------
    Modul Name  : Database Newsltter
    Desc        : null
    Sub fungsi  : null

------------------------------------------------------------*/


class Mdl_newsletter extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table      = 'newsletter';
    protected $primaryKey = 'id';

    protected $allowedFields = ['email'];

    protected $returnType = 'object';
    protected $useTimestamps = false;


    public function add($mdata)
    {
        try {
            $query = $this->insert($mdata);

            if (!$query) {
                return (object) [
                    'code'    => 400,
                    'message' => 'Failed to subscribe. Please check your email and try again.'
                ];
            }
        } catch (\Exception $e) {
            return (object) [
                'code'    => 500,
                'message' => $e->getMessage()
            ];
        }

        return (object) [
            'code'    => 201,
            'message' => 'Subscription successful! You are now subscribed to our newsletter.'
        ];
    }
}
