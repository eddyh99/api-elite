<?php

namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Member extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->member  = model('App\Models\V1\Mdl_member');
        $this->subscribe  = model('App\Models\V1\Mdl_subscribe');
        $this->withdraw  = model('App\Models\V1\Mdl_withdraw');
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

    public function getMembership_history() {
        $member_id = filter_var($this->request->getVar('member_id'), FILTER_SANITIZE_NUMBER_INT);
        $result = $this->subscribe->getMembership_history($member_id);

        if (@$result->code != 200) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(200, "member", null, $result->message), 200);
    }

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

    public function postAdd_freemember()
    {
        $data = $this->request->getJSON();
        $refmember = $this->member->getby_refcode($data->referral);
        if (!$refmember->exist) {
            return $this->respond(error_msg(400, "member", '01', $refmember->message), 400);
        }

        $mdata = [
            'member' => [
                'email'       => trim($data->email),
                'id_referral' => $refmember->id,
            ],
            'subscription' => [
                'initial_capital' => trim($data->amount),
                'status'     => 'free',
                'start_date' => date("Y-m-d H:i:s"),
                'end_date'   => trim($data->expired)
            ]
        ];
        $result = $this->member->freemember_add($mdata);

        if (@$result->code != 201) {
			return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
		}

        return $this->respond(error_msg(201, "member", null, $result->message), 201);
    }

    public function getFree_member() {
        $result = $this->member->get_freemember();
        return $this->respond(error_msg($result->code, "member", null, $result->message), $result->code);
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

    public function postAdd_admin()
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
            'alias' => [
                'rules'  => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required'      => 'Alias is required',
                    'min_length' => 'Alias must be at least 3 characters long.',
                    'max_length' => 'Alias must not exceed 50 characters.',
                ]
            ],
            'role' => [
                'rules'  => 'required|in_list[admin,manager,superadmin]',
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
            ],
            'access' => [
                'rules'  => 'required|is_array',
                'errors' => [
                    'required' => 'Admin access is required.',
                    'is_array' => 'Admin access must be an array.'
                ]
            ]

        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }

        $data           = $this->request->getJSON();
        $allowedMenus = ["subscriber", "freemember", "signal", "payment"];
        if (!empty(array_diff($data->access, $allowedMenus))) {
            return $this->respond(error_msg(400, "member", '01', "Invalid menu access"), 400);
        }

        $mdata = array(
            "member" => [
                "email"     => trim($data->email),
                "passwd"    => trim($data->password),
                "role"      => trim($data->role),
                "status"    => 'active',
                "timezone"  => $data->timezone,
                'ip_addr'    => $data->ip_address
            ],
            "member_role"  => [
                "alias"    => $data->alias,
                'access' => json_encode($data->access)
            ]
        );

        $result = $this->member->admin_add($mdata);

        if (@$result->code != 201) {
            return $this->respond(error_msg(400, "member", '01', $result->message), 400);
        }

        return $this->respond(error_msg(201, "member", null, $result->message), 201);
    }
    
}
