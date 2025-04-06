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


    // +++++++++++++++++

    public function getHistory_payment() {
        $member_id = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
        $result = $this->withdraw->history($member_id);

        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(200, "member", null, $result->data), 200);
    }

    public function getReferral_summary()
    {
        $member_id = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
        $commission = $this->withdraw->getAvailable_commission($member_id);
        $referral = $this->withdraw->get_downline($member_id);

        if (in_array(500, [$commission->code, $referral->code]) || !$member_id) {
            return $this->respond(error_msg(400, "member", '01', 'Data retrieval failed'), 400);
        }

        $mdata = [
            'referral' => $referral->data->downline,
            'commission' => $commission->data->balance,
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

    public function getGet_membership() {
        $result = $this->member->getMembership();
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

    public function getList_commission() {
        $id_member = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
        $result = $this->commission->get_commission_byId($id_member);

        if (@$result->code != 200) {
            return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
        }

        return $this->respond(error_msg(200, "member", null, $result->data), 200);
    }

}
