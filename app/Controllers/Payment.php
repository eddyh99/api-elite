<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Payment extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {

        $this->member       = model('App\Models\V1\Mdl_member');
        $this->setting      = model('App\Models\V1\Mdl_settings');
        $this->deposit      = model('App\Models\V1\Mdl_deposit');
        $this->commission   = model('App\Models\V1\Mdl_commission');
        $this->withdraw     = model('App\Models\V1\Mdl_withdraw');
    }

    public function postDeposit() 
    {
		$data           = $this->request->getJSON();
        $member         = $this->member->getby_email(trim($data->email))->message;
        $referral       = $this->setting->get("referral_fee")->message;
        
        $uplineId = null;
        
        // Jika ada member_id di request, cek dulu
        if (!empty($data->member_id)) {
            $memberById = $this->member->getby_id($data->member_id)->message;
            $uplineId   = $memberById->id_referral ?? null;
        }
        
        // Jika tidak ada atau null, fallback ke $member
        if (empty($uplineId)) {
            $uplineId = null;
        }
        
        $mdata = array(
            "invoice"   => 'INV-' . strtoupper(bin2hex(random_bytes(4))),
            "upline_id" => $uplineId,
			"member_id" => trim($member->id),
			"amount"    => trim($data->amount),
			"commission"=> trim($data->amount) * $referral
		);
        
        $result = $this->deposit->add_balance($mdata);
        if (@$result->code != 201) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(201, "member", null, $mdata["invoice"]), 201);
    }


    public function postUpdate_status()
    {
        $data           = $this->request->getJSON();
        $mdata = [
            'invoice'   => trim($data->invoice),
            'status'    => trim($data->status)
        ];

        $result = $this->deposit->update_status($mdata);
        if ($result->code !== 201) {
			return $this->respond(error_msg($result->code, "subs", '01', $result->message), $result->code);
		}

        $deposit = $this->deposit->getDeposit_byInvoice($data->invoice)->message;
        // wd ke trade
        // if ($deposit->id_referral) {
            $comission = [
                [
                    'member_id' => $deposit->id_referral ?? 1,
                    'withdraw_type' => 'usdt',
                    'amount' => $deposit->commission,
                    'jenis' => 'comission'
                ],
                [
                    'member_id' => $deposit->id_referral ?? 1,
                    'withdraw_type' => 'usdt',
                    'amount' => $deposit->commission,
                    'jenis' => 'trade'
                ],
            ];

            $this->withdraw->insert_withdraw($comission);
        // }

        return $this->respond(error_msg(201, "subs", "01", $result->message), 201);
    }

}
