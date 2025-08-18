<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;

/*----------------------------------------------------------
    Modul Name  : Database Mediation Calculator
    Desc        : null
    Sub fungsi  : null

------------------------------------------------------------*/


class Mdl_calc_mediation extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table            = 'calculator_mediation';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'object';
    protected $useTimestamps = false; // karena hanya ada created_at (otomatis MySQL)

    protected $allowedFields    = [
        'prezzo_buy1',
        'prezzo_buy2',
        'prezzo_buy3',
        'prezzo_buy4',
        'prezzo_sell1',
        'prezzo_sell2',
        'prezzo_sell3',
        'prezzo_sell4',
        
        // field lock
        'lock_buy1',
        'lock_buy2',
        'lock_buy3',
        'lock_buy4',
        'lock_sell1',
        'lock_sell2',
        'lock_sell3',
        'lock_sell4',
        
        'created_at'
    ];


}
