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

    public function createUsBank()
    {
        $data = $this->request->getJSON();

        $validate = $this->validate([
            'us_bank_account_name'   => 'required',
            'us_bank_account_type'   => 'required|in_list[checking,saving]',
            'us_bank_routing_number' => 'required|numeric',
            'us_bank_account_number' => 'required|numeric',
            'us_bank_fee_setting'    => 'required|numeric',
        ]);

        if (! $validate) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $mdata = [
            'us_bank_account_name'   => trim($data->us_bank_account_name),
            'us_bank_account_type'   => trim($data->us_bank_account_type),
            'us_bank_routing_number' => trim($data->us_bank_routing_number),
            'us_bank_account_number' => trim($data->us_bank_account_number),
            'us_bank_fee_setting'    => trim($data->us_bank_fee_setting)
        ];

        $existing = $this->setting->get('us_bank_account_number');
        if (!empty($existing->message) && $existing->message == $mdata['us_bank_account_number']) {
            return $this->fail([
                'message' => 'US Bank account number already exists'
            ], 409);
        }

        $result = $this->setting->createBankAccount($mdata);

        if ($result->success) {
            return $this->respondCreated($result);
        }

        return $this->fail($result->message, $result->code);
    }

    public function postUpdateUsBankAccount()
    {
        $data = $this->request->getJSON();
        
        $validate = $this->validate([
            'us_bank_account_name'    => 'required',
            'us_bank_account_type'    => 'required|in_list[saving,checking]',
            'us_bank_routing_number'  => 'required|numeric',
            'us_bank_account_number'  => 'required|numeric',
            'us_bank_fee_setting'     => 'required|numeric'
        ]);
        
        if (! $validate) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        
        $mdata = [
            'us_bank_account_name'   => trim($data->us_bank_account_name),
            'us_bank_account_type'   => trim($data->us_bank_account_type),
            'us_bank_routing_number' => trim($data->us_bank_routing_number),
            'us_bank_account_number' => trim($data->us_bank_account_number),
            'us_bank_fee_setting'    => trim($data->us_bank_fee_setting)
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

    public function postUpdateInternationalBankAccount()
    {
        $data = $this->request->getJSON();

        $validate = $this->validate([
            'inter_bank_account_name'   => 'required',
            'inter_bank_account_number' => 'required|numeric',
            'inter_swift_code'          => 'required|numeric',
            'inter_fee_setting'         => 'required|numeric',
        ]);

        if (! $validate) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $mdata = [
            'inter_bank_account_name'   => trim($data->inter_bank_account_name),
            'inter_bank_account_number' => trim($data->inter_bank_account_number),
            'inter_swift_code'          => trim($data->inter_swift_code),
            'inter_fee_setting'         => trim($data->inter_fee_setting)
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

    public function getUsBankAccount()
    {
        $result = $this->setting->getUsBankAccount();

        if (empty((array)$result)) {
            return $this->respond([
                'success' => false,
                'code'    => 404,
                'message' => ' US Bank account data not found'
            ], 404);
        }

        return $this->respond([
            'success' => true,
            'code'    => 200,
            'message' => 'US Bank accounts retrieved successfully',
            'data'    => $result
        ], 200);
    }

    public function createInternationalBank()
    {
        $data = $this->request->getJSON();

        $validate = $this->validate([
            'inter_bank_account_name'   => 'required',
            'inter_bank_account_number' => 'required|numeric',
            'inter_swift_code' => 'required|numeric',
            'inter_fee_setting'    => 'required|numeric',
        ]);

        if (! $validate) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $mdata = [
            'inter_bank_account_name'   => trim($data->inter_bank_account_name),
            'inter_bank_account_number' => trim($data->inter_bank_account_number),
            'inter_swift_code'          => trim($data->inter_swift_code),
            'inter_fee_setting'         => trim($data->inter_fee_setting)
        ];

        $existing = $this->setting->get('inter_bank_account_number');
        if (!empty($existing->message) && $existing->message == $mdata['inter_bank_account_number']) {
            return $this->fail([
                'message' => 'International Bank account number already exists'
            ], 409);
        }

        $result = $this->setting->createBankAccount($mdata);

        if ($result->success) {
            return $this->respondCreated($result);
        }

        return $this->fail($result->message, $result->code);
    }

    public function getInternationalBankAccount()
    {
        $result = $this->setting->getInternationalBankAccount();

        if (empty((array)$result)) {
            return $this->respond([
                'success' => false,
                'code'    => 404,
                'message' => ' International Bank account data not found'
            ], 404);
        }

        return $this->respond([
            'success' => true,
            'code'    => 200,
            'message' => 'International Bank accounts retrieved successfully',
            'data'    => $result
        ], 200);
    }

    public function getUsBankFee()
    {
        $result = $this->setting->getUsBankFee();

        if (empty((array)$result)) {
            return $this->respond([
                'success' => false,
                'code'    => 404,
                'message' => 'US bank fee data not found'
            ], 404);
        }

        return $this->respond([
            'success' => true,
            'code'    => 200,
            'message' => 'US bank fees retrieved successfully',
            'data'    => $result
        ], 200);
    }

    public function getInternationalBankFee()
    {
        $result = $this->setting->getInternationalBankFee();

        if (empty((array)$result)) {
            return $this->respond([
                'success' => false,
                'code'    => 404,
                'message' => 'International bank fee data not found'
            ], 404);
        }

        return $this->respond([
            'success' => true,
            'code'    => 200,
            'message' => 'International bank fees retrieved successfully',
            'data'    => $result
        ], 200);
    }
}
