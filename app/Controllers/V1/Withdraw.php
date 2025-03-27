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
        $this->member  = model('App\Models\V1\Mdl_member');
    }

    public function postRequest_payment()
    {

        $data = $this->request->getJSON(true);
        $wallet = $data['wallet_address'] ?? null;
        $details = array_diff_key($data, array_flip(['amount', 'wallet_address', 'member_id', 'type']));
        $amount = $this->withdraw->getAvailable_commission($data['member_id'])->data;

        if ($amount->balance <= 0) {
			return $this->respond(error_msg(400, "withdraw", "01", "Insufficient balance."), 400);
		}

        $mdata = [
            'member_id' => $data['member_id'],
            'withdraw_type' => $data['type'],
            'amount' => $amount->balance,
            'wallet_address' => $wallet,
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
}
