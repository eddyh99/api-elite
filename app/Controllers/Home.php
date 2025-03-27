<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;

class Home extends BaseController
{
    public function index()
    {
        // dd(sha1('miftahus@gmail.com7b902e6ff1db9f560443f2048974fd7d386975b0'));
        // return sha1('admin1234');
        throw PageNotFoundException::forPageNotFound();
    }
}
