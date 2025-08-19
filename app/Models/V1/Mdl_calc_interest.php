<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;

/*----------------------------------------------------------
    Module Name  : Database Interest Calculator
    Description  : Model untuk akses tabel calculator_interest
    Sub-fungsi  : CRUD calculator_interest
------------------------------------------------------------*/

class Mdl_calc_interest extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table            = 'calculator_interest';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'object';
    protected $useTimestamps    = false; 

    protected $allowedFields    = [
        'amount',
        'lock_amount',
        'created_at'
    ];
}
