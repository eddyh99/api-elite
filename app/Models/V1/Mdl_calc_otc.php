<?php

namespace App\Models\V1;

use CodeIgniter\Model;
use Exception;

/*----------------------------------------------------------
    Modul Name  : Database OTC Calculator
    Desc        : Model untuk akses tabel calculator_otc
    Sub fungsi  : CRUD calculator_otc
------------------------------------------------------------*/

class Mdl_calc_otc extends Model
{
    protected $server_tz = "Asia/Singapore";
    protected $table            = 'calculator_otc';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'object';
    protected $useTimestamps    = false; 

    protected $allowedFields    = [
        'amount_btc',
        'lock_amount_btc',
        'buy_price',
        'lock_buy_price',
        'sell_price',
        'lock_sell_price',
        'created_at'
    ];
}
