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
        $this->deposit  = model('App\Models\V1\Mdl_deposit');
        $this->commission  = model('App\Models\V1\Mdl_commission');
        $this->wallet  = model('App\Models\V1\Mdl_wallet');
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

        if (@$sell->code != 200) {
            log_message('info', 'SELL ORDER: ' . json_encode($buy));
            if ($buy->code == 500) {
                return $this->respond(error_msg(500, "signal", '01', $buy->message), 500);
            }
        }

        // Ambil data order
        $orders = array_merge(
            !empty($buy->message) ? [$buy->message] : [],
            !empty($sell->message) ? $sell->message : []
        );

        // Jika tidak ada order yang ditemukan
        if (empty($orders)) {
            return $this->respond(error_msg(200, "buy/sell", null, 'No pending orders found!'), 200);
        }

        $mdata = [];
        foreach($orders as $order) {
            $status = $this->updateOrder($order->order_id);
            $mdata[] = $status->order;

            if($status->side == 'SELL') {
                $this->take_profit($status->cummulativeQuoteQty, $status->order['order_id']);
            }
        } 

        $result = $this->signal->updateStatus_byOrder($mdata);
        if (@$result->code != 201) {
            return $this->respond(error_msg($result->code, "buy", "01", $result->message), $result->code);
        }

        return $this->respond(error_msg(201, "buys", null, $result->message), 201);
    }

    private function updateOrder($order_id)
    {
        $url = BINANCEAPI . "/order";
        $params = [
            "symbol" => "BTCUSDT",
            "orderId" => $order_id
        ];
    
        $response = binanceAPI($url, $params);
    
        // Cek apakah respons valid dan statusnya 'filled'
        if(isset($response->orderId)) {
            $is_filled = $response->status === 'FILLED' ? 'filled' : 'pending';
            $side = $response->side;
            $cummulativeQuoteQty = $response->cummulativeQuoteQty;
        }
    
        $result =  (object) [
            'order' => [
                'status' => $is_filled ?? 'pending',
                'order_id' => $order_id,
            ],
            'side'  => $side ?? null,
            'cummulativeQuoteQty' => $cummulativeQuoteQty ?? 0
        ]; 
        
        log_message('info', 'STATUS ORDER: ' . json_encode($result));
        return $result;
    }

    private function take_profit($amount, $order_id)
    {
        $member = $this->deposit->get_amount_member();
        if ($member->code != 200) {
            return false;
        }

        $profit = [];
        $commission = [];
        foreach ($member->message as $m) {

            $m_profit = (($m->amount / 4) / 100) * $amount;
            $m_commission =  $m_profit * 0.1;
            $netProfit = $m_profit - $m_commission;

            $profit[] = [
                'member_id'         => $m->member_id,
                'master_wallet'     => round($netProfit / 2, 2),
                'client_wallet'     => round($netProfit / 2, 2),
                'order_id'          => $order_id
            ];

            if (!is_null($m->upline)) {
                $commission[] = [
                    'member_id'      => $m->upline,
                    'downline_id'    => $m->member_id,
                    'amount'         => round($m_commission, 2),
                ];
            }
        }

        $update_profit = $this->wallet->add_profits($profit);
        log_message('info', 'MEMBER PROFIT: ' . json_encode($update_profit));

        if (!empty($commission)) {
            $update_commission = $this->commission->add_balances($commission);
            log_message('info', 'MEMBER COMMISSION: ' . json_encode($update_commission));
        }
    }
}
