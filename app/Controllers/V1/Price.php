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
        $price = $this->setting->get('price');
        $commission = $this->setting->get('commission');

        if ($price->code != 200 || $commission->code != 200) {
            return $this->respond(error_msg($price->code, "auth", "01", $price->message), $price->code);
        }

        $mdata = [
            'price' => $price->message,
            'commission' => $commission->message
        ];
        return $this->respond(error_msg(200, "auth", null, $mdata), 200);
    }
}
