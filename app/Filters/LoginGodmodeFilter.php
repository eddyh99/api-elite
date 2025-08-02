<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LoginGodmodeFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('logged_user');
        if (!$user) {
            header("Location:" . BASE_URL . 'godmode/auth/signin');
            exit();
        }
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            session()->setFlashdata('failed', 'Access Denied');
            header("Location:" . BASE_URL . 'member/dashboard');
            exit();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}