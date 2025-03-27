<?php

namespace App\Commands;

use App\Controllers\V1\Order;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateOrder extends BaseCommand
{
    protected $group       = 'custom';
    protected $name        = 'update:order';
    protected $description = 'Auto update order buy and sell';

    public function run(array $params)
    {
        helper('trade_helper');
        $order = new Order();
        $buy = $order->getUpdate_buy();
        // $sell = $order->getUpdate_sell();

        CLI::write((string) $buy, 'green');
    }
}
