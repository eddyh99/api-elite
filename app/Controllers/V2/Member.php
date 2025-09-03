<?php

namespace App\Controllers\V2;

use App\Controllers\BaseController;
use App\Models\V1\Mdl_member_o2o;
use CodeIgniter\API\ResponseTrait;

class Member extends BaseController
{
	use ResponseTrait;

	public function __construct()
	{
		$this->member_o2o  = new Mdl_member_o2o();
	}

	public function getGet_all()
	{
		$result = $this->member_o2o->get_all();
		return $this->respond(error_msg($result->code, "member", null, $result->message), $result->code);
	}

	public function getGet_membership()
	{
		$result = $this->member_o2o->getMembership();
		if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

		return $this->respond(error_msg(200, "member", null, $result->data), 200);
	}

	public function postGet_detailmember()
	{
		$email = $this->request->getJSON()->email ?? null;
		$result = $this->member_o2o->detail_member_byEmail($email);

		if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

		return $this->respond(error_msg(200, "member", null, $result->data), 200);
	}

	public function postSet_status()
	{
		$data = $this->request->getJSON();
		$mdata = [
			"email" => $data->email,
			"status" => $data->status
		];

		$result = $this->member_o2o->set_status($mdata);
		if (@$result->code != 201) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

		return $this->respond(error_msg(201, "member", null, $mdata), 201);
	}

	public function postDestroy()
	{
		$email = $this->request->getJSON()->email ?? null;

		$mdata = [
			'email' => $email,
			'new_email' => $email . '_' . date('Y-m-d')
		];
		$result = $this->member_o2o->deleteby_email($mdata);

		if (@$result->code != 201) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

		return $this->respond(error_msg(201, "member", null, $result->message), 201);
	}

	public function getList_downline()
	{
		$id_member = filter_var($this->request->getVar('id_member'), FILTER_SANITIZE_NUMBER_INT);
		$result = $this->member_o2o->get_downline_byId($id_member);

		if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

		return $this->respond(error_msg(200, "member", null, $result->data), 200);
	}
	
}
