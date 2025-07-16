<?php

namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Onetoone extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->memberonetoone       = model('App\Models\V1\Mdl_member_onetoone');
        $this->paymentonetoone  = model('App\Models\V1\Mdl_payment_onetoone');
    }

    public function getIndex()
    {

        $result = $this->memberonetoone->get_all();
        if (empty($result)) {
            return $this->failNotFound('No data found.');
        }
        return $this->respond($result);
    }

    public function getList_payment()
    {
        $result = $this->paymentonetoone->get_all();
        return $this->respond($result, $result->code ?? 500);
    }

    public function postAdd_member_onetoone()
    {
        $data = $this->request->getJSON(true);
        $validation = $this->validation;
        $validation->setRules([
            'email' => [
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => 'Email is required',
                    'valid_email' => 'Invalid Email format'
                ]
            ]
        ]);
        $email = $data['email'] ;
        if (!$validation->withRequest($this->request)->run()) {
            return $this->fail($validation->getErrors());
        }

        $mdata = [
            'email' => $email,
            'is_deleted' => 0,
        ];
        $result = $this->memberonetoone->insert_memberonetoone($mdata);

        return $this->respond($result, $result->code ?? 500);
    }
}
