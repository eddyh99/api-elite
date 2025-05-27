<?php

namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Withdraw extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->withdraw  = model('App\Models\V1\Mdl_withdraw');
        $this->member    = model('App\Models\V1\Mdl_member');
        $this->deposit   = model('App\Models\V1\Mdl_deposit');
        $this->wallet    = model('App\Models\V1\Mdl_wallet');
        $this->signal    = model('App\Models\V1\Mdl_signal');
    }

    public function postRequest_payment()
    {

        $data = $this->request->getJSON(true);
        $wallet = $data['wallet_address'] ?? null;
        $details = array_diff_key($data, array_flip(['amount', 'wallet_address', 'member_id', 'type']));

        $mdata = [
            'member_id' => $data['member_id'],
            'withdraw_type' => $data['type'],
            'amount' => $data['amount'],
            'wallet_address' => $wallet,
            'jenis' => 'withdraw',
            'payment_details' => json_encode($details)

        ];
        $result = $this->withdraw->insert_withdraw($mdata);

        if (@$result->code != 201) {
			return $this->respond(error_msg($result->code, "withdraw", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(201, "withdraw", null, $result->message), 201);

    }

    public function getRequest_payment() {
        $result = $this->withdraw->list_withdraw();

        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "withdraw", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(200, "withdraw", null, $result->data), 200);
    }

    public function getDetail_request_payment() {
        $id = filter_var($this->request->getVar('id'), FILTER_SANITIZE_NUMBER_INT);
        $result = $this->withdraw->getDetail_withdraw($id);

        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "withdraw", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(200, "withdraw", null, $result->data), 200);
    }

    public function postUpdate_status()
    {
        $data = $this->request->getJSON();
        $member = $this->member->getby_email($data->email);

        if (@$member->code != 200) {
            return $this->respond(error_msg($member->code, "withdraw", "01", $member->message), $member->code);
        }

        $mdata = [
            'member_id' => $member->message->id,
            'data' => [
                'status' => $data->status,
                'admin_notes' => $data->notes ?? null,
            ]
        ];

        $result = $this->withdraw->update_status($mdata);

        if (@$result->code != 201) {
            return $this->respond(error_msg($result->code, "withdraw", "01", $result->message), $result->code);
        }

        return $this->respond(error_msg(201, "withdraw", null, $result->message), 201);
    }

    public function postAvailable_commission() {
        $data = $this->request->getJSON();
        $result = $this->withdraw->getAvailable_commission($data->member_id);

        if (@$result->code != 200) {
            return $this->respond(error_msg($result->code, "withdraw", "01", $result->message), $result->code);
        }

        return $this->respond(error_msg(200, "withdraw", null, $result->data), 200);
    }

    public function postTransfer_balance() {
        $validation = $this->validation;
        $validation->setRules([
            'id_member' => [
                'rules'  => 'required'
            ],
            'destination' => [
                'rules'  => 'required|in_list[trade,fund]',
            ],
            'coin' => [
                'rules'  => 'required|in_list[usdt,btc]',
            ],
            'amount' => [
                'rules' => 'required'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }

        $data           = $this->request->getJSON();
        //jika tujuan transfer trade, ambil cek balance dari fund wallet
        if ($data->destination=="trade"){
            $amount = $this->deposit->getBalance_byIdMember($data->id_member);
        }elseif ($data->destination=="fund"){
        //jika tujuan transfer fund, ambil cek balance dari trade wallet
            $amount = $this->wallet->getBalance_byIdMember($data->id_member);
        }

        // check pending sell
        if ($data->destination == "fund" && $data->coin == 'btc') {
            if ($this->signal->checkPending_sell()->message == true) {
                return $this->respond(error_msg(400, "Sell", null, "Cannot process because there is a pending sell order."), 400);
            }
        }        

        // Make sure amount is provided
        if (!empty($data->amount)) {
            // Determine which coin to compare
            $coin = strtolower($data->coin ?? ''); // 'usdt' or 'btc'
            $balance = 0;
        
            if ($coin === 'usdt') {
                $balance = $amount->message->usdt ?? '0';
            } elseif ($coin === 'btc') {
                $balance = $amount->message->btc ?? '0';
            }
        
            // Convert to string to avoid float issues
            $userAmount = number_format((float)$data->amount, 8, '.', '');
            $balanceAmount = number_format((float)$balance, 8, '.', '');

            // Compare using bccomp with appropriate precision
            if (bccomp($userAmount, $balance, 8) === 1) {
                // user amount > balance
                return $this->respond(error_msg(400, "transfer", "01",'Insufficient Balance' ), 400);
            }
        
            $mdata = [
                'member_id' => $data->id_member,
                'withdraw_type' => $data->coin == 'usdt' ? 'usdt' : 'btc',
                'amount' => $data->amount ?? 0,
                'jenis' => $data->destination == 'fund' ? 'balance' : 'trade'
    
            ];
            $result = $this->withdraw->insert_withdraw($mdata);
    
            if (@$result->code != 201) {
    			return $this->respond(error_msg($result->code, "transfer", "01", $result->message), $result->code);
    		}
    
            return $this->respond(error_msg(201, "transfer", null, "Transfer balance is processed"), 201);
        }
    }
}
