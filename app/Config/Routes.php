<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
 $routes->group('', static function ($routes) {
    $routes->get('price', 'V1\Price::getIndex');
    $routes->get('price/profit', 'V1\Price::getProfit');
    $routes->get('price/detail_profit', 'V1\Price::getDetail_profit');
});
$routes->get('/', 'Home::index');
$routes->group('non', static function ($routes) {
    $routes->post('notify_payment', 'Payment::postUpdate_status');
    $routes->post('deposit', 'Payment::postDeposit');
});