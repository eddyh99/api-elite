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


//api tanpa filter
$routes->group('apiv1', static function ($routes) {
    $routes->get('onetoone/member/get_all', 'V1\Onetoone::getIndex');
    $routes->post('onetoone/member/add', 'V1\Onetoone::postAdd_member_onetoone');
    $routes->get('onetoone/list_payment', 'V1\Onetoone::getList_payment');
});
