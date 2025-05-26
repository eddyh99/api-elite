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
            $mdata['order'][] = $status->order;

            if ($status->side === 'SELL' && $status->order['status'] == 'filled') {
                $takeProfitData = $this->take_profits($status->cummulativeQuoteQty, $order->order_id, $order->pair_id);
                $profits = array_merge($profits, $takeProfitData['profits']);
                $commissions = array_merge($commissions, $takeProfitData['commissions']);

                // isi pair_id buy!
                $mdata['pair_id'][] = [
                    'id' => $order->pair_id,
                    'pair_id' => $order->pair_id
                ];
            }

            // jika bukan buy a maka update amount di pnglobal
            if ($status->side === 'BUY' && $status->order['status'] == 'filled') {

                // update amount BTC
                // $this->updateAmountBTC();

                // update order pnglobal
                if($order->type != 'Buy A') {
                    $this->updateOrdersAll('pnglobal', $order);
                }
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
        // Retrieve buy orders based on the selected pair
        $signal_buy = $this->signal->get_orders($pair_id);
    
        // If the response is not successful
        if ($signal_buy->code !== 200) {
            return ['profits' => [], 'commissions' => []];
        }
    
        // Get the total amount (in USDT) used for the buy orders
        $buy_amount = $signal_buy->message[0]->total_usdt;
    
        $profits = [];
        $commissions = [];
    
        
        foreach ($signal_buy->message as $m) {
            // Calculate profit (difference between sell and buy)
            $total_profit = $sell_amount - $buy_amount;
    
            // Calculate member profit
            $m_profit = ($m->amount_usdt / $buy_amount) * $total_profit;
    
            // 10% commission
            $m_commission = $m_profit * 0.1;
    
            // Net profit 
            $netProfit = $m_profit - $m_commission;
    
            // Split the net profit equally between master and client wallets
            $profits[] = [
                'member_id' => $m->member_id,
                'master_wallet' => round($netProfit / 2, 2),
                'client_wallet' => round($netProfit / 2, 2),
                'order_id' => $order_id
            ];
    
            // If the member has an upline
            if (!is_null($m->upline)) {
                $commissions[] = [
                    'member_id' => $m->upline,
                    'downline_id' => $m->member_id,
                    'amount' => round($m_commission, 2),
                ];
            }
        }
    
        // Return the final profit and commission distributions
        return ['profits' => $profits, 'commissions' => $commissions];
    }
    
    private function updateOrdersAll($type, $signal) {

        // pnglobal update order
        $order = new Order;
        $last_order = $order->getlast_order($signal->type);

        $mdata = [
            'type' => $signal->type,
            'id_signal'   =>  $signal->id,
            'latsorder_ids' => explode(',', $last_order->ids)
        ];

        $url = URLPNGLOBAL . '/updateorder';
        $result = sendRequest($url, $mdata);

        log_message('info', 'UPDATE ORDER PNGLOBAL RESPONSE: ' . json_encode($result));
    }

    // private function updateAmountBTC($order) {
    //     $member = $this->deposit->getMember_tradeBalance();
    //     if ($member->code != 200) {
    //         return false;
    //     }

    //     function convertBTC($number, $precision = 6) {
    //         $factor = pow(10, $precision);
    //         return floor($number * $factor) / $factor;
    //     }

    //     $mdata = [];
    //     foreach ($member->message as $m) {
    //         $percent = ($m->trade_balance) / $order->cummulativeQuoteQty;
    //         $btc     = $order->origQty * $percent;

    //         $mdata[] = [
    //             'member_id' => $m->member_id,
    //             'amount_usdt' => $order->cummulativeQuoteQty * $percent,
    //             'amount_btc' => convertBTC($btc, 6)
    //         ];
    //     }
    //     return $mdata;
    // }
    
}
