<?php

use CodeIgniter\Config\Services;

if (!function_exists('check_access')) {
    function check_access($access = null)
    {
        $session = session();
        $user = $session->get('logged_user');

        $router = Services::router();
        $fullController = $router->controllerName();
        $parts = explode('\\', $fullController);
        $controller = strtolower(end($parts));
        $method = $access ?? strtolower($router->methodName());

        if ($user->role !== 'superadmin') {
            $access = json_decode($user->access, true);
            if (empty($access[$method]) || !in_array($controller, $access[$method])) {
                $session->setFlashdata('failed', 'Access Denied');
                header("Location: " . BASE_URL . 'godmode/signal');
                exit();
            }
        }
    }
}
