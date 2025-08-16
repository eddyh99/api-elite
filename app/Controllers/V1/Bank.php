<?php

namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Bank extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->setting  = model('App\Models\V1\Mdl_settings');
    }

    public function getIndex()
    {
        $result = $this->setting->getBankAccount();

        if (empty((array)$result)) {
            return $this->respond([
                'success' => false,
                'code'    => 404,
                'message' => 'Bank account data not found'
            ], 404);
        }

        return $this->respond([
            'success' => true,
            'code'    => 200,
            'message' => 'Bank accounts retrieved successfully',
            'data'    => $result
        ], 200);
    }

    public function postUpdate_bankaccount()
    {
        $data = $this->request->getJSON();

        $validate = $this->validate([
            'bank_account_name'    => 'required',
            'bank_account_type'    => 'required|in_list[saving,checking]',
            'bank_routing_number'  => 'required|numeric',
            'bank_account_number'  => 'required|numeric'
        ]);

        if (! $validate) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $mdata = [
            'bank_account_name'   => trim($data->bank_account_name),
            'bank_account_type'   => trim($data->bank_account_type),
            'bank_routing_number' => trim($data->bank_routing_number),
            'bank_account_number' => trim($data->bank_account_number)
        ];

        $result = $this->setting->updateBankAccount($mdata);

        if (empty($result->success) || $result->success === false) {
            return $this->respond(
                error_msg($result->code, "auth", '01', $result->message),
                $result->code
            );
        }

        return $this->respond($result);
    }
}
