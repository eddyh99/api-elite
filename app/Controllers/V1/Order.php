<?php

namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Order extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->deposit  = model('App\Models\V1\Mdl_deposit');
        $this->signal  = model('App\Models\V1\Mdl_signal');
        $this->member_signal  = model('App\Models\V1\Mdl_member_signal');
        $this->proxy  = model('App\Models\V1\Mdl_proxies');
    }

    public function postLimit_buy()
    {

        $validation = $this->validation;
        $validation->setRules([
            'type' => [
                'rules'  => 'required|in_list[BUY A,BUY B,BUY C,BUY D]',
                'errors' => [
                    'required' => 'Type is required',
                    'in_list'  => 'Invalid type, allowed types: BUY A, BUY B, BUY C, BUY D'
                ]
            ],
            'limit' => [
                'rules'  => 'required|numeric|greater_than[0]',
                'errors' =>  [
                    'required'     => 'Limit is required',
                    'numeric'      => 'Limit must be a number',
                    'greater_than' => 'Limit must be greater than 0'
                ]
            ],
            'admin_id' => [
                'rules'  => 'required|integer',
                'errors' => [
                    'required'     => 'Admin ID is required',
                    'integer'      => 'Admin ID must be an integer',
                ]
            ],
            'ip_address' => [
                'rules'  => 'required|valid_ip',
                'errors' => [
                    'required'  => 'IP Address is required',
                    'valid_ip'  => 'Invalid IP address format'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }

        $data    = $this->request->getJSON();
        $deposit  = $this->deposit->get_amount();

        if (@$deposit->code != 200) {
            return $this->respond(error_msg(400, "signal", '01', $deposit->message), 400);
        }

        $order = $this->limit_order('BUY', $deposit->message, $data->limit);

        if (!isset($order->orderId) || !isset($order->origQty)) {
            return $this->respond(error_msg(400, "order", '01', 'Order Failed'), 400);
        }        

        $mdata = [
            'admin_id' => $data->admin_id,
            'ip_addr'  => $data->ip_address,
            'type'     => $data->type,
            'order_id' => $order->orderId,
            'entry_price' => $data->limit
        ];
        $signal = $this->signal->add($mdata);
        if (@$signal->code != 201) {
            return $this->respond(error_msg(400, "signal", '01', $signal->message), 400);
        }
        
        $member = $this->getBTC_member($order->origQty, $signal->id);
        $member_signal = $this->member_signal->add($member);
        if (@$member_signal->code != 201) {
            return $this->respond(error_msg(400, "signal", '01', $member_signal->message), 400);
        }

        return $this->respond(error_msg(201, "order", null, $signal->message), 201);

    }

    private function getBtc_member($amount, $signal_id)
    {
        $member = $this->deposit->get_amount_member();
        if ($member->code != 200) {
            return false;
        }

        $mdata = [];
        foreach ($member->message as $m) {
            $mdata[] = [
                'member_id' => $m->member_id,
                'amount_btc' => (($m->amount / 4) / 100) * $amount,
                'sinyal_id' => $signal_id
            ];
        }
        return $mdata;
    }

    public function postLimit_sell()
    {

        $validation = $this->validation;
        $validation->setRules([
            'type' => [
                'rules'  => 'required|in_list[SELL A,SELL B,SELL C,SELL D]',
                'errors' => [
                    'required' => 'Type is required',
                    'in_list'  => 'Invalid type, allowed types: SELL A, SELL B, SELL C, SELL D'
                ]
            ],
            'id_signal' => [
                'rules'  => 'required|is_numeric',
                'errors' => [
                    'required' => 'Type is required',
                ]
            ],
            'limit' => [
                'rules'  => 'required|numeric|greater_than[0]',
                'errors' =>  [
                    'required'     => 'Limit is required',
                    'numeric'      => 'Limit must be a number',
                    'greater_than' => 'Limit must be greater than 0'
                ]
            ],
            'admin_id' => [
                'rules'  => 'required|integer',
                'errors' => [
                    'required'     => 'Admin ID is required',
                    'integer'      => 'Admin ID must be an integer',
                ]
            ],
            'ip_address' => [
                'rules'  => 'required|valid_ip',
                'errors' => [
                    'required'  => 'IP Address is required',
                    'valid_ip'  => 'Invalid IP address format'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }

    }


    // fixx
    public function limit_order($side, $amount, $limit)
    {

        $stepSize = 0.00001000;

        if ($side == 'BUY') {  // =========buy
            $btc = $amount / $limit;
            $quantityBTC = floor($btc / $stepSize) * $stepSize;
        } else {              // =========sell    
            $quantityBTC = $amount;
        }

        $params =  [
            "symbol"      => "BTCUSDT",
            "side"        => $side,
            "type"        => "LIMIT",
            "timeInForce" => "GTC",
            "quantity"    => $quantityBTC,
            "price"       => $limit,
            "newClientOrderId" => 'order_' . bin2hex(random_bytes(10))
        ];

        $url = BINANCEAPI . '/order';
        $result = binanceAPI($url, $params, 'POST');
        return $result;
    }

    public function postFill_order()
    {
        $id = filter_var($this->request->getVar('id_signal'), FILTER_SANITIZE_NUMBER_INT);
        $result = $this->signal->fill_order($id);
        if (@$result->code != 201) {
            return $this->respond(error_msg($result->code, "order", '01', $result->message), $result->code);
        }

        return $this->respond(error_msg(201, "order", null, $result->message), 201);
    }

    public function getGet_all()
    {
        $result = $this->signal->get_all();
        if (@$result->code != 200) {
            return $this->respond(error_msg($result->code, "order", '01', $result->message), $result->code);
        }

        return $this->respond(error_msg(200, "order", null, $result->message), 200);
    }

    public function get_proxies()
    {
        $result = $this->proxy->get_all();

        if (@$result->code != 200) {
            return false;
        }

        // filter proksi yang aktif
        $active_proxies = $this->get_active_proxies($result->message);

        if (empty($active_proxies)) {
            return false;
        }

        return $active_proxies;
    }

    private function get_active_proxies($proxies)
    {
        $mh = curl_multi_init();
        $handles = [];

        foreach ($proxies as $proxy) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://1.1.1.1/cdn-cgi/trace");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_PROXY, "{$proxy->ip_address}:{$proxy->port}");
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

            if (!empty($proxy->username) && !empty($proxy->password)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, "{$proxy->username}:{$proxy->password}");
            }

            curl_multi_add_handle($mh, $ch);
            $handles[$proxy->ip_address . ':' . $proxy->port] = ['ch' => $ch, 'proxy' => $proxy];
        }

        // Eksekusi semua request secara paralel
        do {
            $status = curl_multi_exec($mh, $active);
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

        // Ambil hanya proxy yang aktif
        $active_proxies = [];
        foreach ($handles as $key => $data) {
            $ch = $data['ch'];
            $proxy = $data['proxy'];

            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            if ($http_code === 200 && empty($error)) {
                $active_proxies[] = $proxy; // Simpan hanya proxy yang aktif
            }

            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }

        curl_multi_close($mh);

        return $active_proxies; // Hanya mengembalikan proxy yang aktif
    }

    public function getDelete()
    {
        return;
    }


    //========= for debugging ===========
    public function getSell_all()
    {
        $qty = $this->request->getVar('qty');
        $url = BINANCEAPI . "/order";
        $params = [
            "symbol"      => "BTCUSDT",
            "side"        => 'SELL',
            "type"        => "MARKET",
            "quantity"    => $qty ?? 0,
        ];

        $response = binanceAPI($url, $params, 'POST');
        if (isset($response->code)) {
            return $this->respond(error_msg(400, "binance", $response->code, $response->msg), 400);
        }

        return $this->respond(error_msg(200, "binance", null, $response), 200);
    }

    public function getBalance()
    {
        $url = BINANCEAPI . "/account";

        $response = binanceAPI($url, []);
        if (isset($response->code)) {
            return $this->respond(error_msg(400, "binance", $response->code, $response->msg), 400);
        }

        return $this->respond(error_msg(200, "binance", null, $response), 200);
    }

    public function getStatus()
    {
        $order_id = $this->request->getVar('order_id');
        $url = BINANCEAPI . "/order";
        $params = [
            "symbol" => "BTCUSDT",
            "orderId" => $order_id
        ];

        $response = binanceAPI($url, $params);
        if (isset($response->code)) {
            return $this->respond(error_msg(400, "binance", $response->code, $response->msg), 400);
        }

        return $this->respond(error_msg(200, "binance", null, $response), 200);
    }

    public function postDelete_all()
    {
        $url = BINANCEAPI . "/openOrders";
        $params = [
            "symbol" => "BTCUSDT"
        ];
        $response = binanceAPI($url, $params, "DELETE");

        if (isset($response->code)) {
            return $this->respond(error_msg(400, "binance", $response->code, $response->msg), 400);
        }

        return $this->respond(error_msg(200, "binance", null, $response), 200);
    }

    public function getShow_all()
    {
        $url = BINANCEAPI . "/openOrders";
        $params = [
            "symbol" => "BTCUSDT"
        ];
        $response = binanceAPI($url, $params);

        if (isset($response->code)) {
            return $this->respond(error_msg(400, "binance", $response->code, $response->msg), 400);
        }

        return $this->respond(error_msg(200, "binance", null, $response), 200);
    }

    public function getTruncate_signals()
    {
        $result = $this->signal->truncate();

        if ($result->code !== 201) {
            return $this->respond(error_msg(400, "binance", $result->code, $result->message), 400);
        }

        return $this->respond(error_msg(201, "binance", null, $result->message), 201);
    }
}
