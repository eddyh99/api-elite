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
        $this->member_signal  = model('App\Models\V1\Mdl_member_signal');
        $this->member  = model('App\Models\V1\Mdl_member');
        $this->setting  = model('App\Models\V1\Mdl_settings');
        $this->withdraw     = model('App\Models\V1\Mdl_withdraw');
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
        $withdraw_trade = [];
        $member_signal = [];
        $member = [];

        foreach ($orders as $order) {
            $status = $this->updateOrder($order->order_id);
            $mdata['order'][] = $status->order;

            $cost = $status->cummulativeQuoteQty;
            $total_usdt = ($cost - $status->commission);
            if ($status->side === 'SELL' && $status->order['status'] == 'filled') {
                $takeProfitData = $this->take_profits($status->origQty, $cost, $order->order_id, $order->id, $order->pair_id, $order->type);
                $profits = array_merge($profits, $takeProfitData['profits']);
                $commissions = array_merge($commissions, $takeProfitData['commissions']);
                $member_signal = array_merge($member_signal, $takeProfitData['member_signal']);
                $withdraw_trade = array_merge($withdraw_trade, $takeProfitData['withdraw_trade']);
                $this->mergeById($member, $takeProfitData['member']);

                // isi pair_id buy!
                $mdata['pair_id'][] = [
                    'id' => $order->pair_id,
                    'pair_id' => $order->pair_id
                ];
            }

            // jika bukan buy a maka update amount di pnglobal
            if ($status->side === 'BUY' && $status->order['status'] == 'filled') {

                // $deposit  = $this->deposit->getTotal_tradeBalance();
                // $trade_balance = ( ($deposit->message + $cost) /4);
                // $trade_balance = $deposit->message;
                $asset_btc = $this->setting->get('asset_btc')->message;
                // log_message('info', 'TRADE BALANCE ORI: ' . json_encode($deposit->message));

                // get btc
                $member_btc = $this->getBtc_member($asset_btc, $order->id, $order->type);
                $member_signal = array_merge($member_signal, $member_btc);

                // update order pnglobal
                if ($order->type != 'Buy A') {
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

            // trasnfer to trade
            $this->withdraw->insert_withdraw($withdraw_trade);
            log_message('info', 'WD COMISSION: ' . json_encode($withdraw_trade));
        }

        // add member signal (sell)
        if (!empty($member_signal)) {
            $this->member_signal->addOrUpdate($member_signal);
            log_message('info', 'MEMBER SIGNAL: ' . json_encode($member_signal));
        }

        // set position 0
        if (!empty($member)) {
            $this->member->update_data($member);
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
        if (isset($response->orderId)) {
            $is_filled = $response->status === 'FILLED' ? 'filled' : 'pending';
            $side = $response->side;
            $cummulativeQuoteQty = $response->cummulativeQuoteQty;
            $origQty = $response->origQty;

            $commission = 0;
            if (!empty($response->fills) && isset($response->fills[0]->commission)) {
                $commission = $response->fills[0]->commission;
            }
        }

        $result =  (object) [
            'order' => [
                'status' => $is_filled ?? 'pending',
                'order_id' => $order_id,
            ],
            'side'  => $side ?? null,
            'cummulativeQuoteQty' => $cummulativeQuoteQty ?? 0,
            'origQty' => $origQty ?? 0,
            'commission' => $commission ?? 0
        ];

        log_message('info', 'STATUS ORDER: ' . json_encode($result));
        return $result;
    }

    private function take_profits($total_btc, $sell_amount, $order_id, $signal_id, $pair_id, $type)
    {
        // Retrieve buy orders based on the selected pair
        $signal_buy = $this->signal->get_orders($pair_id);

        // If the response is not successful
        if ($signal_buy->code !== 200) {
            return ['profits' => [], 'commissions' => [], 'member_signal' => []];
        }

        $profits = [];
        $commissions = [];
        $member_signal = [];
        $member = [];
        $withdraw_trade = [];
        $side_position = [
            'Sell A' => 'position_a',
            'Sell B' => 'position_b',
            'Sell C' => 'position_c',
            'Sell D' => 'position_d'
        ];


        foreach ($signal_buy->message as $m) {
            // Calculate profit (difference between sell and buy)
            $amount_usdt = ($m->amount_btc / $total_btc) * $sell_amount;
            $profit = $amount_usdt - $m->amount_usdt; //margin


            $cost = $this->setting->get('cost_trade')->message ?? 0.01;
            $client_wallet = ($profit - ($profit * $cost)) / 2;
            $master_wallet = $profit - $client_wallet;

            $member_signal[] = [
                'member_id'    => $m->member_id,
                'amount_btc'   => $m->amount_btc,
                'amount_usdt'  => $amount_usdt,
                'sinyal_id'    => $signal_id
            ];

            $member[] = [
                'id' => $m->member_id,
                $side_position[$type] => 0
            ];

            // Split the net profit equally between master and client wallets
            $profit_data = [
                'member_id' => $m->member_id,
                'master_wallet' => round($master_wallet, 2),
                'client_wallet' => round($client_wallet, 2),
                'order_id' => $order_id
            ];

            // If the member has an upline
            if (!is_null($m->upline)) {
                $commission = $client_wallet * 0.1;
                $profit_data['master_wallet'] = round($master_wallet - $commission, 2);
                $commissions[] = [
                    'member_id' => $m->upline,
                    'downline_id' => $m->member_id,
                    'amount' => round($commission, 2),
                    'order_id' => $order_id
                ];

                // wd trade
                $withdraw_trade[] = [
                    'member_id' => $m->upline,
                    'withdraw_type' => 'usdt',
                    'amount' => round($commission, 2),
                    'jenis' => 'comission'
                ];

                $withdraw_trade[] = [
                    'member_id' => $m->upline,
                    'withdraw_type' => 'usdt',
                    'amount' => round($commission, 2),
                    'jenis' => 'trade'
                ];
            }

            $profits[] = $profit_data;
        }


        // Return the final profit and commission distributions
        return ['member' => $member, 'profits' => $profits, 'commissions' => $commissions, 'member_signal' => $member_signal, 'withdraw_trade' => $withdraw_trade];
    }

    private function updateOrdersAll($type, $signal)
    {

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

    // 
    public function getAssets($coin)
    {
        $url = BINANCEAPI . "/account";

        $response = binanceAPI($url, []);
        if (isset($response->code)) {
            return false;
        }

        $btc = array_values(array_filter($response->balances, function ($bal) use ($coin) {
            return $bal->asset === $coin;
        }));
        return $btc[0];
    }

    private function getBtc_member($asset_btc, $signal_id, $type)
    {

        function convertBTC($number, $precision = 5)
        {
            $factor = pow(10, $precision);
            return floor($number * $factor) / $factor;
        }

        $btc = $this->getAssets("BTC");
        $amount_btc = convertBTC(($btc->free + $btc->locked));
        // log_message('info', 'Trade Balance: ' .json_encode($trade_balance));
        // log_message('info', 'cost: ' .json_encode($cost));
        if ($type == 'Buy A') {
            $amount_btc -= ($asset_btc + 0);
            // $this->setting->store(['asset_btc' => $asset_btc ]);
        } else {
            $prev_signal = $this->signal->getPrev_signals($type)->message;
            $amount_btc -= ($asset_btc + $prev_signal->btc);
            log_message('info', 'BTC FROM PREV BUY: ' . json_encode($prev_signal));
        }


        $member = $this->member_signal->getby_sinyal($signal_id);
        if ($member->code != 200) {
            return [];
        }

        $mdata = [];
        foreach ($member->message as $m) {
            $btc     = (($m->amount_usdt) / $m->total_usdt) * $amount_btc;
            // log_message('info', 'AMOUNT USDT: ' . json_encode($m->amount_usdt));
            // log_message('info', 'TOTAL USDT: ' . json_encode($m->total_usdt));
            // log_message('info', 'AMOUNT BTC: ' . json_encode($amount_btc));
            $mdata[] = [
                'member_id' => $m->member_id,
                'amount_btc' => convertBTC($btc, 5),
                'amount_usdt' => $m->amount_usdt,
                'sinyal_id' => $signal_id
            ];
        }
        return $mdata;
    }

    private function mergeById(&$target, $source)
    {
        foreach ($source as $row) {
            $id = $row['id'];
            if (!isset($target[$id])) {
                $target[$id] = ['id' => $id];
            }
            foreach ($row as $key => $val) {
                if ($key !== 'id') {
                    $target[$id][$key] = $val;
                }
            }
        }
    }
}
