<?php

namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Member extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->member       = model('App\Models\V1\Mdl_member');
        $this->setting      = model('App\Models\V1\Mdl_settings');
        $this->deposit      = model('App\Models\V1\Mdl_deposit');
        $this->commission   = model('App\Models\V1\Mdl_commission');
        $this->withdraw     = model('App\Models\V1\Mdl_withdraw');
        $this->wallet     = model('App\Models\V1\Mdl_wallet');
        $this->member_signal  = model('App\Models\V1\Mdl_member_signal');
    }

    public function getGet_all()
    {
        $result = $this->member->get_all();
        return $this->respond(error_msg($result->code, "member", null, $result->message), $result->code);
    }

    public function getGet_admin()
    {
        $result = $this->member->get_admin();
        return $this->respond(error_msg($result->code, "member", null, $result->message), $result->code);
    }

    public function getMaster_trade(){
        $result = $this->deposit->masterPosition();
        return $this->respond(error_msg(200, "member", null, $result), 200);
    }
    
    public function getGet_totalbalance()
    {
        $result = $this->member->getTotal_balance();
        return $this->respond(error_msg($result->code, "member", null, $result->message), $result->code);
    }

    public function postBalance()
    {
        $validation = $this->validation;
        $validation->setRules([
            'id_member' => [
                'rules'  => 'required'
            ],
            'type' => [
                'rules'  => 'required|in_list[trade,commission,fund]',
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }

        $data           = $this->request->getJSON();
        $id_member      = $data->id_member;

        switch ($data->type) {
            case 'fund':
                $result = $this->deposit->getBalance_byIdMember($id_member);
                break;
            case 'commission':
                $result = $this->commission->getBalance_byId($id_member);
                break;
            case 'trade':
                $result = $this->wallet->getBalance_byIdMember($id_member);
                break;
        }

        if (empty($result) || $result->code != 200) {
            return $this->respond(error_msg($result->code ?? 400, "balance", "01", $result->message ?? 'An error occurred'), $result->code ?? 400);
        }

        return $this->respond(error_msg(200, "balance", null, $result->message), 200);
    }


    public function getHistory_deposit() {
        $member_id = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
        $result = $this->deposit->history($member_id);

        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(200, "member", null, $result->message), 200);
    }

    public function getHistory_trade() {
        $member_id = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
        if($member_id) {
            $result = $this->member->history_trade($member_id);
        } else {
            $result = $this->member->history_trades();
        }

        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(200, "member", null, $result->message), 200);
    }


    // +++++++++++++++++

    public function getHistory_payment() {
        $member_id = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
        $result = $this->withdraw->history_payment($member_id);

        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(200, "member", null, $result->data), 200);
    }

    public function getReferral_summary()
    {
        $member_id = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
        $commission = $this->commission->getBalance_byId($member_id);
        $referral = $this->withdraw->get_downline($member_id);

        if (in_array(500, [$commission->code, $referral->code]) || !$member_id) {
            return $this->respond(error_msg(400, "member", '01', 'Data retrieval failed'), 400);
        }

        $mdata = [
            'referral' => $referral->data->downline,
            'commission' => $commission->message->usdt,
        ];

        return $this->respond(error_msg(200, "member", null, $mdata), 200);
    }

    public function getReferral_mastersummary()
    {
        $referral = $this->withdraw->get_downline();

        $mdata = [
            'referral' => $referral->data->downline,
            'commission' => 0,
        ];

        return $this->respond(error_msg(200, "member", null, $mdata), 200);
    }


    public function postDestroy()
    {
        $email = $this->request->getJSON()->email ?? null;

        $mdata = [
            'email' => $email,
            'new_email' => $email . '_' . date('Y-m-d')
        ];
        $result = $this->member->deleteby_email($mdata);

        if (@$result->code != 201) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(201, "member", null, $result->message), 201);
    }

    public function getGet_statistics() {
        $result = $this->member->getStatistics();
        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(200, "member", null, $result->data), 200);
    }

    public function postSet_status() {
        $data = $this->request->getJSON();
        $mdata = [
            "email" => $data->email,
            "status" => $data->status
        ];

        $result = $this->member->set_status($mdata);
        if (@$result->code != 201) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(201, "member", null, $mdata), 201);
    }

    public function postGet_detailmember()
    {
        $email = $this->request->getJSON()->email ?? null;
        $result = $this->member->detail_member_byEmail($email);

        if (@$result->code != 200) {
            return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
        }

        return $this->respond(error_msg(200, "member", null, $result->data), 200);
    }

    public function getList_downline() {
        $id_member = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
        $result = $this->member->get_downline_byId($id_member);

        if (@$result->code != 200) {
            return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
        }

        return $this->respond(error_msg(200, "member", null, $result->data), 200);
    }

    public function getList_masterdownline() {
        $result = $this->member->get_downline_byId();

        if (@$result->code != 200) {
            return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
        }

        return $this->respond(error_msg(200, "member", null, $result->data), 200);
    }

    public function getReferralmember() {
        $result = $this->member->get_referral_member();

        if (@$result->code != 200) {
            return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
        }

        return $this->respond(error_msg(200, "member", null, $result->data), 200);
    }

    public function getList_commission() {
        $id_member = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
        $result = $this->commission->get_commission_byId($id_member);

        if (@$result->code != 200) {
            return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
        }

        return $this->respond(error_msg(200, "member", null, $result->data), 200);
    }

    public function postTransfer_commission()
    {
        $validation = $this->validation;
        $validation->setRules([
            'id_member' => [
                'rules'  => 'required'
            ],
            'destination' => [
                'rules'  => 'required|in_list[fund,trade]',
            ],
            'amount' => [
                'rules' => 'required'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }

        $data           = $this->request->getJSON();
    
        $idMember = $data->id_member;
        $destination = $data->destination;
    
        // Ambil balance commission
        $commission = $this->commission->getBalance_byId($idMember);
        if (!isset($commission->code) || $commission->code != 200) {
            return $this->respond(error_msg(400, "commission", "01", 'Failed to get available commission'), 400);
        }

        $balance_commission =  $commission->message->usdt;
        if($balance_commission <= 0 || $balance_commission < $data->amount) {
            return $this->respond(error_msg(400, "commission", "01", 'Insufficient balance'), 400);
        }
    
        // Lanjut transfer
        $mdata = [[
            'member_id' => $idMember,
            'withdraw_type' => 'usdt',
            'amount' => $data->amount,
            'jenis' => 'comission'
        ]];

        if($destination == 'trade') {
            $mdata[] = [
                'member_id' => $idMember,
                'withdraw_type' => 'usdt',
                'amount' => $data->amount,
                'jenis' => 'trade'
            ];
        }
        $result = $this->withdraw->insert_withdraw($mdata);
    
        if (!isset($result->code) || $result->code != 201) {
            return $this->respond(error_msg($result->code, "transfer", "01", $result->message), $result->code);
        }
    
        return $this->respond(error_msg(201, "member", null, $result->message), 201);
    }
    
    public function getList_activemember(){
        $result = $this->member->get_activemember();
        return $this->respond(error_msg($result->code, "member", null, $result->message), $result->code);
    }
    
    public function getList_transaction() {
        $member_id = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
//        if(!empty($member_id)) {
            $result = $this->member->list_transaction($member_id);
/*        } else {
            $result = $this->member->list_transactions();
        }
*/
        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(200, "member", null, $result->message), 200);
    }
    
    public function getList_comission(){
        $member_id = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
        $result = $this->commission->list_commission($member_id);
        return $this->respond(error_msg(200, "member", null, $result), 200);
    }

    public function getList_comission2(){
        $member_id = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
        $result = $this->commission->get_commission_byId($member_id);
        return $this->respond(error_msg(200, "member", null, $result), 200);
    }
    
    public function postAdmin_deposit(){
        $amount = filter_var($this->request->getVar('amount'), FILTER_SANITIZE_NUMBER_INT);
        $mdata=array(
                "invoice"   => "INV".time(),
                "member_id" => 1,
                "amount"    => $amount,
                "commission" => 0,
                "created_at" => date("Y-m-d H:i:s"),
                "status"     => 'complete'
            );
        
        $result = $this->deposit->deposit_admin($mdata);
        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
    }

    public function getList_mastercomission(){
        $result = $this->commission->get_commission_byId();
        return $this->respond(error_msg(200, "member", null, $result), 200);
    }

    public function postUpdate_refcode()
    {
        $data   = $this->request->getJSON();
        $mdata = [
            [
                'id' => $data->idmember,
                'role'  => 'referral',
                'refcode' => $data->refcode
            ]
        ];

        $result = $this->member->update_data($mdata);
        if (@$result->code != 200) {
            return $this->respond(error_msg(400, "member", "01", $result->message), 400);
        }

        return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
    }

}
