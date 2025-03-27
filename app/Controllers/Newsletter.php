<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Newsletter extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->newsletter  = model('App\Models\V1\Mdl_newsletter');
    }

    public function getAdd()
    {
        $email = filter_var($this->request->getVar('email'), FILTER_VALIDATE_EMAIL);

        if (!$email) {
            return $this->respond(error_msg(400, "newsletter", null, 'Invalid email address. Please enter a valid email.'), 400);
        }

        $result = $this->newsletter->add(['email' => $email]);

        if (@$result->code != 201) {
            return $this->respond(error_msg($result->code, "member", "01", $result->message), $result->code);
        }

        return $this->respond(error_msg(201, "newsletter", null, $result->message), 201);
    }
}
