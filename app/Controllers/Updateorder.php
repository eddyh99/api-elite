<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\V1\Order;
use CodeIgniter\API\ResponseTrait;

class Updateorder extends BaseController
{
    use ResponseTrait;
    protected $order;

    public function __construct()
    {

        $this->signal  = model('App\Models\V1\Mdl_signal');
        $this->order = new Order();
    }

    public function getIndex()
    {
        $mdata = [];
        return $mdata;
    }

    private function updateBuy($signal, $proxies)
    {
        return;
    }

    private function updateSell($signal, $proxies)
    {
        return;
    }
}
