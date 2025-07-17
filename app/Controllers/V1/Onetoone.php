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

    public function getMemberbyId($id){
        $result = $this->memberonetoone->get_by_id($id);

        return $this->respond($result, $result->code ?? 500);
    }

    public function postAdd_member_onetoone()
    {
        $data = $this->request->getJSON(true);
        $validation = $this->validation;
        $validation->setRules([
            'email' => [
                'rules'  => 'required|valid_email|is_unique[tb_member_onetone.email]',
                'errors' => [
                    'required'    => 'Email is required',
                    'valid_email' => 'Invalid Email format',
                    'is_unique'   => 'Email already exists'
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

    public function postDelete_member_onetoone($id = null)
    {

        // Proses delete
        $result = $this->memberonetoone->delete_memberonetoone($id);

        return $this->respond($result, $result->code ?? 500);
    }


    public function getList_payment()
    {
        $result = $this->paymentonetoone->get_all();
        return $this->respond($result, $result->code ?? 500);
    }

    public function postPayment(){
        $request = $this->request->getJSON();

        $member = $this->memberonetoone->get_by_email($request->email);
        if (!$member) {
            return $this->respond([
                'status' => false,
                'message' => 'Member with that email not found'
            ], 404);
        }

        // return response()->setJSON([
        //     'status' => true,
        //     'message' => 'Member found',
        //     'data' => $member
        // ]);

        $data = [
            'id_member_onetoone' => $member['id'],
            'status_invoice' => $request->status_invoice,
            'link_invoice'   => $request->link_invoice,
            'invoice_date'   => $request->invoice_date
        ];

        $result = $this->paymentonetoone->insert_payment($data);
        return $this->respond($result, $result->code ?? 500);
    }
}
