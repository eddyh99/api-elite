<?php

namespace App\Controllers;

use App\Controllers\BaseController;
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
        $profits = [];
        $commissions = [];

        foreach($orders as $order) {
            $status = $this->updateOrder($order->order_id);
            $mdata[] = $status->order;

            if ($status->side === 'SELL' && $status->order['status'] == 'filled') {
                $takeProfitData = $this->take_profits($status->cummulativeQuoteQty, $order->order_id, $order->pair_id);
                $profits = array_merge($profits, $takeProfitData['profits']);
                $commissions = array_merge($commissions, $takeProfitData['commissions']);
            }
        } 

        // Update Profits
        if (!empty($profits)) {
            $this->wallet->add_profits($profits);
            log_message('info', 'MEMBER PROFIT: ' . json_encode($profits));
        }
    
        // Update Commission
        if (!empty($commissions)) {
            $this->commission->add_balances($commissions);
            log_message('info', 'MEMBER COMMISSION: ' . json_encode($commissions));
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

    private function take_profits($sell_amount, $order_id, $pair_id)
    {
        $signal_buy = $this->signal->get_orders($pair_id);
        if ($signal_buy->code !== 200) {
            return ['profits' => [], 'commissions' => []];
        }

        $buy_amount = $signal_buy->message[0]->total_usdt;
    
        $profits = [];
        $commissions = [];
        foreach ($signal_buy->message as $m) {
            $total_profit = $sell_amount - $buy_amount;
            $m_profit = ($m->amount_usdt / $buy_amount) * $total_profit;
            $m_commission = $m_profit * 0.1;
            $netProfit = $m_profit - $m_commission;
    
            $profits[] = [
                'member_id' => $m->member_id,
                'master_wallet' => round($netProfit / 2, 2),
                'client_wallet' => round($netProfit / 2, 2),
                'order_id' => $order_id
            ];
    
            if (!is_null($m->upline)) {
                $commissions[] = [
                    'member_id' => $m->upline,
                    'downline_id' => $m->member_id,
                    'amount' => round($m_commission, 2),
                ];
            }
        }
    
        return ['profits' => $profits, 'commissions' => $commissions];
    }
    
}
