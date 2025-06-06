<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('price', 'V1\Price::getIndex');
$routes->get('/', 'Home::index');
$routes->group('non', static function ($routes) {
    $routes->post('notify_payment', 'Payment::postUpdate_status');
    $routes->post('deposit', 'Payment::postDeposit');
});