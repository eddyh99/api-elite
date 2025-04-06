<?php

namespace App\Controllers\V1;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Price extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->setting  = model('App\Models\V1\Mdl_settings');
    }

    public function getIndex()
    {
        $mdata = [
            'price' => $this->setting->get('price')->message,
            'cost'  => $this->setting->get('cost')->message,
            'referral_fee' => $this->setting->get('referral_fee')->message
        ];
        return $this->respond(error_msg(200, "auth", null, $mdata), 200);
    }
}
