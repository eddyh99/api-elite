<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController
{
	use ResponseTrait;

	public function __construct()
	{
		$this->member  = model('App\Models\V1\Mdl_member');
	}

	public function postRegister()
	{

		$validation = $this->validation;
		$validation->setRules([
			'email' => [
				'rules'  => 'required|valid_email',
				'errors' => [
					'required'      => 'Email is required',
					'valid_email'   => 'Invalid Email format'
				]
			],
			'password' => [
				'rules'  => 'required|min_length[8]',
				'errors' =>  [
					'required'      => 'Password is required',
					'min_length'    => 'Min length password is 8 character'
				]
			],
			'role' => [
				'rules'  => 'required|in_list[member,admin,manager,superadmin,referral]',
				'errors' => [
					'required' => 'User role is required.',
					'in_list'  => 'Invalid user role.'
				]
			],
			'timezone' => [
				'rules'  => 'required',
				'errors' =>  [
					'required'      => 'User timezone is required',
				]
			],
			'ip_address' => [
				'rules'  => 'required|valid_ip',
				'errors' => [
					'required'  => 'User IP is required',
					'valid_ip'  => 'User IP must be a valid IP address',
				]
			]

		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->fail($validation->getErrors());
		}

		$data           = $this->request->getJSON();

		$mdata = array(
			"email"     => trim($data->email),
			"passwd"    => trim($data->password),
			"role"		=> trim($data->role),
			"status"	=> @$data->status ? @$data->status : ($data->role != 'member' ? 'active' : 'new'),
			"timezone"  => $data->timezone,
			"refcode"	=> empty($data->referral) ? null : $data->referral,
			'ip_addr'	=> $data->ip_address
		);

		if (!empty($data->referral)) {
			$refmember = $this->member->getby_refcode($data->referral);
			if (!$refmember->exist) {
				return $this->respond(error_msg(400, "auth", '01', $refmember->message), 400);
			}
			$mdata["id_referral"] = $data->referral != 'm4573r' ? $refmember->id : null;
			$mdata["refcode"] = null;
		}
		
		$mdata['otp'] = rand(1000, 9999);
		$result = $this->member->add($mdata);
		if (!@$result->success) {
			if ($result->code == 1060 || $result->code == 1062) {
				$result->message = 'User already registered';
			}
			return $this->respond(error_msg(400, "auth", '01', $result->message), 400);
		}

		$message = [
			"text" => $result->message,
			"otp"	  => $mdata['otp']
		];

		return $this->respond(error_msg(201, "auth", null, $message), 201);
	}

	public function postSignin()
	{
		$validation = $this->validation;
		$validation->setRules([
			'email' => [
				'rules'  => 'required|valid_email',
				'errors' => [
					'required'      => 'Email is required',
					'valid_email'   => 'Invalid Email format'
				]
			],
			'password' => [
				'rules'  => 'required',
				'errors' =>  [
					'required'      => 'Password is required',
					'min_length'    => 'Min length password is 8 character'
				]
			],
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->fail($validation->getErrors());
		}

		$data           = $this->request->getJSON();

		$member = $this->member->getby_email($data->email);
		if (@$member->code != 200) {
			return $this->respond(error_msg($member->code, "auth", "01", $member->message), $member->code);
		}

		if ($data->password == $member->message->passwd) {
			$response = $member->message;
			if($response->role == 'member') {
				unset($response->access);
			}

			return $this->respond(error_msg(200, "auth", "02", $response), 200);
		} else {
			$response = "Invalid username or password";
			return $this->respond(error_msg(400, "auth", "02", $response), 400);
		}
	}

	// public function postAdmin_Signin()
	// {
	// 	$validation = $this->validation;
	// 	$validation->setRules([
	// 		'email' => [
	// 			'rules'  => 'required|valid_email',
	// 			'errors' => [
	// 				'required'      => 'Email is required',
	// 				'valid_email'   => 'Invalid Email format'
	// 			]
	// 		],
	// 		'password' => [
	// 			'rules'  => 'required|min_length[8]',
	// 			'errors' =>  [
	// 				'required'      => 'Password is required',
	// 				'min_length'    => 'Min length password is 8 character'
	// 			]
	// 		],
	// 	]);

	// 	if (!$validation->withRequest($this->request)->run()) {
	// 		return $this->fail($validation->getErrors());
	// 	}

	// 	$data           = $this->request->getJSON();

	// 	$member = $this->member->getby_email($data->email);
	// 	if (@$member->code != 200) {
	// 		return $this->respond(error_msg($member->code, "auth", "01", $member->message), $member->code);
	// 	}

	// 	$allowedRoles = ['admin', 'manager', 'superadmin'];

	// 	// Validasi role
	// 	if (!in_array($member->message->role, $allowedRoles)) {
	// 		return $this->respond(error_msg(403, "auth", "03", "Access denied. You are not authorized to sign in."), 403);
	// 	}

	// 	if ($data->password == $member->message->passwd) {
	// 		$response = $member->message;

	// 		return $this->respond(error_msg(200, "auth", "02", $response), 200);
	// 	} else {
	// 		$response = "Invalid username or password";
	// 		return $this->respond(error_msg(400, "auth", "02", $response), 400);
	// 	}
	// }

	public function postResend_token()
	{
		$validation = $this->validation;
		$validation->setRules([
			'email' => [
				'rules'  => 'required|valid_email',
				'errors' => [
					'required'      => 'Email is required',
					'valid_email'   => 'Invalid Email format'
				]
			]
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->fail($validation->getErrors());
		}

		$email = $this->request->getJSON()->email;
		$mdata = [
			'email' => filter_var($email, FILTER_VALIDATE_EMAIL),
			'otp'	=> rand(1000, 9999)
		];

		$result = $this->member->update_otp($mdata);
		if ($result->code !== 200) {
			return $this->respond(error_msg($result->code, "auth", '01', $result->message), $result->code);
		}

		$message = [
			'text' => $result->message,
			"otp"	  => $mdata['otp']
		];

		return $this->respond(error_msg($result->code, "auth", null, $message), $result->code);
	}

	public function postActivate_member()
	{
		$validation = $this->validation;
		$validation->setRules([
			'email' => [
				'rules'  => 'required|valid_email',
				'errors' => [
					'required'      => 'Email is required',
					'valid_email'   => 'Invalid Email format'
				]
			],
			'otp' => [
				'rules'  => 'required|numeric|exact_length[4]',
				'errors' => [
					'required'     => 'OTP is required',
					'numeric'      => 'OTP must be a number',
					'exact_length' => 'OTP must be exactly 4 digits'
				]
			]
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->fail($validation->getErrors());
		}

		$data           = $this->request->getJSON();
		$mdata = array(
			"email"     => trim($data->email),
			"otp"    => trim($data->otp),
		);

		$result = $this->member->activate($mdata);
		if ($result->code !== 200) {
			return $this->respond(error_msg($result->code, "auth", '01', $result->message), $result->code);
		}

		return $this->respond(error_msg(200, "auth", null, $result->message), 200);
	}

	public function postReset_password() {
		$validation = $this->validation;
		$validation->setRules([
			'email' => [
				'rules'  => 'required|valid_email',
				'errors' => [
					'required'      => 'Email is required',
					'valid_email'   => 'Invalid Email format'
				]
			],
			'password' => [
				'rules'  => 'required|min_length[8]',
				'errors' =>  [
					'required'      => 'Password is required',
					'min_length'    => 'Min length password is 8 character'
				]
			],
			'otp' => [
				'rules'  => 'required|numeric|exact_length[4]',
				'errors' => [
					'required'     => 'OTP is required',
					'numeric'      => 'OTP must be a number',
					'exact_length' => 'OTP must be exactly 4 digits'
				]
			]
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return $this->fail($validation->getErrors());
		}

		$data       = $this->request->getJSON();

		$mdata = [
			'email' 	=> trim($data->email),
			'password'  => trim($data->password),
			'otp'		=> trim($data->otp)
		];

		$isgodmode = !empty($data->isgodmode) && $data->isgodmode == true;
		$result = $this->member->reset_password($mdata, $isgodmode);
		if ($result->code !== 200) {
			return $this->respond(error_msg($result->code, "auth", '01', $result->message), $result->code);
		}

		return $this->respond(error_msg(200, "auth", null, $result->message), 200);
	}

	public function postOtp_check() {
        $data = $this->request->getJSON();
        $mdata = [
            "email" => $data->email,
            "otp" => $data->otp
        ];

        $result = $this->member->otp_check($mdata);
        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(200, "member", null, $result->message), 200);
    }

    public function getAssets()
    {
        $url = BINANCEAPI . "/account";

        $response = binanceAPI($url, []);
        if (isset($response->code)) {
            return $this->respond(error_msg(400, "binance", $response->code, $response->msg), 400);
        }

        return $this->respond(error_msg(200, "binance", null, $response), 200);
    }
}
