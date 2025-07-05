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
                $takeProfitData = $this->take_profits($status->origQty, $cost, null, $order->order_id, $order->id, $order->pair_id, $order->type);
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

    private function take_profits($total_btc = null, $sell_amount = null, $entry_price = null, $order_id, $signal_id, $pair_id, $type)
    {
        // helper: truncate decimal-string to $dec places (no rounding, just chop)
        $bcTruncate = function(string $numStr, int $dec): string {
            // divide by 1 with scale = $dec → chops extra digits
            return bcdiv($numStr, '1', $dec);
        };
        
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
    
        $costRate = (string) ($this->setting->get('cost_trade')->message ?? '0.01');
        log_message('info', 'COST ' . $costRate);

        foreach ($signal_buy->message as $m) {
            // --- 1) gross USD = amount_btc * entry_price (8 dp)
            $amount_usdt = bcmul((string)$m->amount_btc, (string)$entry_price, 8);            
            log_message('info', 'BTC ' . json_encode($m->amount_btc));
            log_message('info', 'Amount USDT ' . json_encode($amount_usdt));

            // --- 2) net after 0.1% fee = gross_usdt * 0.999 (8 dp)
            $net_amount = bcmul($amount_usdt, '0.999', 8);            
            log_message('info', 'NET USDT ' . json_encode($net_amount));

            // --- 3) profit margin = net_amount - original_usdt (8 dp)
            $profit = bcsub($net_amount, (string)$m->amount_usdt, 8);
            log_message('info', 'PROFIT MEMBER ID: ' . $m->member_id . ' | PROFIT: ' . number_format((float)$profit, 8, '.', ''));

            // --- 4) after cost split = profit * (1 - costRate) / 2 (8 dp)
            $afterCost      = bcmul($profit, bcsub('1', $costRate, 8), 8);
            $client_wallet  = bcdiv($afterCost, '2', 8);
            
            // --- 5) truncate client & master to 2 dp
            $master_wallet = bcsub($profit, $client_wallet, 8);
            log_message('info', 'Client Wallet ' . $client_wallet);
            log_message('info', 'Master Wallet No Split' . $master_wallet);

            $member_signal[] = [
                'member_id'    => $m->member_id,
                'amount_btc'   => $m->amount_btc,
                'amount_usdt'  => $bcTruncate($net_amount, 2),
                'sinyal_id'    => $signal_id
            ];

            $member[] = [
                'id' => $m->member_id,
                $side_position[$type] => 0
            ];

            // Split the net profit equally between master and client wallets
            $profit_data = [
                'member_id' => $m->member_id,
                'master_wallet' => $bcTruncate($master_wallet,2),
                'client_wallet' => $bcTruncate($client_wallet,2),
                'order_id' => $order_id
            ];
            log_message('info', 'Profit Data ' . json_encode($profit));
            log_message('info', 'upline ' . json_encode($m->upline));

            // If the member has an upline
            if (!is_null($m->upline)) {
                $commission = bcmul($client_wallet, '0.1', 8);

                log_message('info', 'Commission ' . json_encode($commission));

                $profit_data['master_wallet'] = $bcTruncate(bcsub($master_wallet, $commission, 8),2);
                log_message('info', 'New Master Wallet ' . json_encode($profit_data['master_wallet']));
                $commissions[] = [
                    'member_id' => $m->upline,
                    'downline_id' => $m->member_id,
                    'amount' => $bcTruncate($commission,2),
                    'order_id' => $order_id
                ];

                // wd trade
                $withdraw_trade[] = [
                    'member_id' => $m->upline,
                    'withdraw_type' => 'usdt',
                    'amount' => $bcTruncate($commission,2),
                    'jenis' => 'comission'
                ];

                $withdraw_trade[] = [
                    'member_id' => $m->upline,
                    'withdraw_type' => 'usdt',
                    'amount' => $bcTruncate($commission,2),
                    'jenis' => 'trade'
                ];
            }

            $profits[] = $profit_data;
            log_message('info', '================ ');
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


    public function getFilled_sell()
{
    $validationRules = [
        'buy_id' => 'required|numeric',
        'filled_price' => 'required|numeric',
        'sell_id' => 'required|numeric'
    ];

    if (!$this->validate($validationRules)) {
        return $this->response->setJSON([
            'status' => 400,
            'message' => $this->validator->getErrors()
        ]);
    }

    $buy_id = $this->request->getVar('buy_id');
    $filled_price = $this->request->getVar('filled_price');
    $sell_id = $this->request->getVar('sell_id');
    $profits = [];
    $commissions = [];
    $withdraw_trade = [];
    $member_signal = [];
    $member = [];

    $signal_sell = $this->signal->get_order($sell_id);

    // If the response is not successful
    if ($signal_sell->code !== 200) {
        return $this->response->setJSON([
            'status' => $signal_sell->code,
            'message' => $signal_sell->message
        ]);
    }

    $sell = $signal_sell->message;
    if ($sell->status == 'filled') {
        return $this->response->setJSON([
            'status' => 400,
            'message' => 'already filled.'
        ]);
    }

    $takeProfitData = $this->take_profits(null, null, $filled_price, $sell->order_id, $sell_id, $buy_id, $sell->type);
    try {
        $profits = array_merge($profits, $takeProfitData['profits']);
        $commissions = array_merge($commissions, $takeProfitData['commissions']);
        $member_signal = array_merge($member_signal, $takeProfitData['member_signal']);
        $withdraw_trade = array_merge($withdraw_trade, $takeProfitData['withdraw_trade']);
        $this->mergeById($member, $takeProfitData['member']);
    } catch (\Throwable $th) {
        return $this->response->setJSON([
            'status' => 400,
            'message' => 'an error occurred.'
        ]);
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

        $mdata = [
            'order' => ['order_id' => $sell->order_id],
            'pair_id' => [
                'pair_id' => $buy_id,
                'id'      => $buy_id
            ]
        ];

        $result = $this->signal->updateStatus_byOrder($mdata);

    return $this->response->setJSON([
        'status' => 200,
        'message' => 'success',
        'data' => $takeProfitData
    ]);
}

public function getFilled_buy() {
    $validationRules = [
        'buy_id' => 'required|numeric',
        'filled_price' => 'required|numeric',
        'type_buy' => 'required|in_list[BUY A,BUY B, BUY C, BUY D]',
    ];

    if (!$this->validate($validationRules)) {
        return $this->response->setJSON([
            'status' => 400,
            'message' => $this->validator->getErrors()
        ]);
    }

    $mdata = [];
    $buy_id = $this->request->getVar('buy_id');
    $type = $this->request->getVar('type_buy');
    $filled_price = $this->request->getVar('filled_price');
    $deposit  = $this->deposit->rebalanceMemberPosition($type);
    if (@$deposit->code != 200) {
        return $this->response->setJSON([
            'status' => 400,
            'message' => $deposit->message
        ]);
    }
    
    function truncateDecimals($number, $decimals = 2) {
        $factor = pow(10, $decimals);
        return floor($number * $factor) / $factor;
    }

    //$totalbtc = ($deposit->message / $filled_price) * (1 - 0.001);
    $member = $this->member->getby_ids($deposit->member_ids);
    $side_position = [
        'BUY A' => 'position_a',
        'BUY B' => 'position_b',
        'BUY C' => 'position_c',
        'BUY D' => 'position_d'
    ];
    $position = $side_position[$type];
    $mdata = [];
    foreach ($member->message as $m) {
        $amount_usdt = $m[$position];
        $btc     = ($amount_usdt / $filled_price) * (1-0.001);
        
        $mdata[] = [
            'member_id' => $m['id'],
            'amount_usdt' => truncateDecimals($amount_usdt),
            'amount_btc' => round($btc,8),
            'sinyal_id' => $buy_id
        ];
    }

    if (!empty($mdata)) {
        $this->member_signal->addOrUpdate($mdata);
    }

    return $this->response->setJSON([
        'status' => 200,
        'message' => 'success',
        'data' => $mdata
    ]);
}
}
