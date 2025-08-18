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

        return $this->respondCreated([
            'success' => true,
            'message' => 'Mediation calculator data created successfully',
            'data'    => $data
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

}
