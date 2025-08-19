<?php

namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Calculator extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->calc_mediation  = model('App\Models\V1\Mdl_calc_mediation');
        $this->calc_otc        = model('App\Models\V1\Mdl_calc_otc');
    }

    // ------- Calculator Mediation -------
    public function getMediationCalculator()
    {
        $result = $this->calc_mediation->first();

        if (empty($result)) {
            return $this->failNotFound('No mediation calculator data found');
        }

        return $this->respond([
            'success' => true,
            'message' => 'Mediation calculator data retrieved successfully',
            'data'    => $result
        ], 200);
    }

    public function postCreateMediationCalculator()
    {

        $result = $this->calc_mediation->first();

        if (!empty($result)) {
            return $this->fail('Mediation calculator data already exists.');
        }

        $data = $this->request->getJSON(true);
        // return $this->respond($data);

        $rules = [
            'prezzo_buy1'  => 'required|decimal',
            'prezzo_buy2'  => 'required|decimal',
            'prezzo_buy3'  => 'required|decimal',
            'prezzo_buy4'  => 'required|decimal',
            'prezzo_sell1' => 'required|decimal',
            'prezzo_sell2' => 'required|decimal',
            'prezzo_sell3' => 'required|decimal',
            'prezzo_sell4' => 'required|decimal',
            'lock_buy1'    => 'permit_empty|in_list[0,1]',
            'lock_buy2'    => 'permit_empty|in_list[0,1]',
            'lock_buy3'    => 'permit_empty|in_list[0,1]',
            'lock_buy4'    => 'permit_empty|in_list[0,1]',
            'lock_sell1'   => 'permit_empty|in_list[0,1]',
            'lock_sell2'   => 'permit_empty|in_list[0,1]',
            'lock_sell3'   => 'permit_empty|in_list[0,1]',
            'lock_sell4'   => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $this->calc_mediation->insert($data);
        $id = $this->calc_mediation->insertID();

        return $this->respondCreated([
            'success' => true,
            'message' => 'Mediation calculator data created successfully',
            'data'    => array_merge(['id' => $id], $data)
        ]);
    }

    public function postUpdateMediationCalculator($id)
    {
        // return $this->respond('sda');
        $mediation = $this->calc_mediation->find($id);

        if (empty($mediation)) {
            return $this->failNotFound('Mediation calculator data not found');
        }

        $data = $this->request->getJSON(true);

        $rules = [
            'prezzo_buy1'  => 'permit_empty|decimal',
            'prezzo_buy2'  => 'permit_empty|decimal',
            'prezzo_buy3'  => 'permit_empty|decimal',
            'prezzo_buy4'  => 'permit_empty|decimal',
            'prezzo_sell1' => 'permit_empty|decimal',
            'prezzo_sell2' => 'permit_empty|decimal',
            'prezzo_sell3' => 'permit_empty|decimal',
            'prezzo_sell4' => 'permit_empty|decimal',
            'lock_buy1'    => 'permit_empty|in_list[0,1]',
            'lock_buy2'    => 'permit_empty|in_list[0,1]',
            'lock_buy3'    => 'permit_empty|in_list[0,1]',
            'lock_buy4'    => 'permit_empty|in_list[0,1]',
            'lock_sell1'   => 'permit_empty|in_list[0,1]',
            'lock_sell2'   => 'permit_empty|in_list[0,1]',
            'lock_sell3'   => 'permit_empty|in_list[0,1]',
            'lock_sell4'   => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $this->calc_mediation->update($id, $data);

        return $this->respond([
            'success' => true,
            'message' => 'Mediation calculator data updated successfully',
            'data'    => $data
        ]);
    }

    public function deleteMediationCalculator($id)
    {
        $mediation = $this->calc_mediation->find($id);

        if (! $mediation) {
            return $this->failNotFound('Mediation calculator data not found');
        }

        $this->calc_mediation->delete($id);

        return $this->respondDeleted([
            'success' => true,
            'message' => 'Mediation calculator data deleted successfully'
        ]);
    }

    // ------- Calculator OTC -------
    public function getOtcCalculator(){
        $result = $this->calc_otc->first();

        if (empty($result)) {
            return $this->failNotFound('No OTC calculator data found');
        }

        return $this->respond([
            'success' => true,
            'message' => 'OTC calculator data retrieved successfully',
            'data'    => $result
        ], 200);
    }

    public function postCreateOtcCalculator()
    {
        $result = $this->calc_otc->first();

        if (!empty($result)) {
            return $this->fail('OTC calculator data already exists.');
        }

        $data = $this->request->getJSON(true);

        $rules = [
            'amount_btc'      => 'required|decimal',
            'lock_amount_btc' => 'permit_empty|in_list[0,1]',
            'buy_price'       => 'required|decimal',
            'lock_buy_price'  => 'permit_empty|in_list[0,1]',
            'sell_price'      => 'required|decimal',
            'lock_sell_price' => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $this->calc_otc->insert($data);
        $id = $this->calc_otc->insertID();

        return $this->respondCreated([
            'success' => true,
            'message' => 'OTC calculator data created successfully',
            'data'    => array_merge(['id' => $id], $data)
        ]);
    }

    public function postUpdateOtcCalculator($id)
    {
        $otc = $this->calc_otc->find($id);

        if (empty($otc)) {
            return $this->failNotFound('OTC calculator data not found');
        }

        $data = $this->request->getJSON(true);

        $rules = [
            'amount_btc'      => 'permit_empty|decimal',
            'lock_amount_btc' => 'permit_empty|in_list[0,1]',
            'buy_price'       => 'permit_empty|decimal',
            'lock_buy_price'  => 'permit_empty|in_list[0,1]',
            'sell_price'      => 'permit_empty|decimal',
            'lock_sell_price' => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Buang field yang kosong string
        foreach ($data as $key => $value) {
            if ($value === "") {
                unset($data[$key]);
            }
        }

        $this->calc_otc->update($id, $data);

        $dataUpdate = $this->calc_otc->find($id);

        return $this->respond([
            'success' => true,
            'message' => 'OTC calculator data updated successfully',
            'data'    => $dataUpdate
        ]);
    }

    public function deleteOtcCalculator($id)
    {
        $otc = $this->calc_otc->find($id);

        if (empty($otc)) {
            return $this->failNotFound('OTC calculator data not found');
        }

        $this->calc_otc->delete($id);

        return $this->respondDeleted([
            'success' => true,
            'message' => 'OTC calculator data deleted successfully'
        ]);
    }

}
