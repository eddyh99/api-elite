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

        $buy = $this->signal->getBuy_pending();

        if (@$buy->code != 200) {
            log_message('info', 'BUY ORDER: ' . json_encode($buy));
            if ($buy->code == 500) {
                return $this->respond(error_msg(500, "signal", '01', $buy->message), 500);
            }
        }

        $sell = $this->signal->getSell_pending();

        if (@$buy->code != 200) {
            log_message('info', 'SELL ORDER: ' . json_encode($buy));
            if ($buy->code == 500) {
                return $this->respond(error_msg(500, "signal", '01', $buy->message), 500);
            }
        }

        $data_buy = $buy->message;
        $data_sell = $sell->message;

        if (!empty($data_buy)) {
            $status = $this->updateBuy($data_buy->order_id);
        } else if (!empty($data_sell)) {
            $status = $this->updateSell($data_sell);
        } else {
            return $this->respond(error_msg(200, "buy/sell", null, 'No pending orders found!'), 200);
        }

        $result = $this->signal->updateStatus_byOrder($status);
        if (@$result->code != 201) {
            return $this->respond(error_msg($result->code, "buy", "01", $result->message), $result->code);
        }

        return $this->respond(error_msg(201, "buys", null, 'Order has been filled'), 201);
    }

    private function updateBuy($order_id)
    {
        $url = BINANCEAPI . "/order";
        $params = [
            "symbol" => "BTCUSDT",
            "orderId" => $order_id
        ];
    
        $response = binanceAPI($url, $params);
    
        // Cek apakah respons valid dan statusnya 'filled'
        $is_filled = isset($response->status) && $response->status === 'FILLED';
    
        return (object) [
            'status' => $is_filled ? 'filled' : 'pending',
            'order_id' => $order_id
        ];
    }

    private function updateSell($signal)
    {
        $is_filled = false;
        return (object) [
            'filled' => $is_filled,
        ];
    }
}
