<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LoginLuxbrokerFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('logged_user');
        if (!$user) {
            header("Location:" . BASE_URL . 'member/auth/login');
            exit();
        }
        if (!in_array($user->role, ['member', 'referral'])) {
            session()->setFlashdata('failed', 'Access Denied');
            header("Location:" . BASE_URL . 'godmode/signal');
            exit();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}