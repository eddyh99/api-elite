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
    $routes->get('onetoone/member/', 'V1\Onetoone::getIndex');
    $routes->get('onetoone/member/(:segment)', 'V1\Onetoone::getMemberbyId/$1');
    $routes->post('onetoone/member/add', 'V1\Onetoone::postAdd_member_onetoone');
    $routes->delete('onetoone/member/delete/(:segment)', 'V1\Onetoone::postDelete_member_onetoone/$1');
    $routes->get('onetoone/list_payment', 'V1\Onetoone::getList_payment');
    $routes->post('onetoone/payment', 'V1\Onetoone::postPayment');
    $routes->put('onetoone/payment', 'V1\Onetoone::putPayment');

    $routes->get('bank', 'V1\Bank::getIndex');
    $routes->post('bank/create', 'V1\Bank::postCreateBankAccount');
    $routes->post('bank/update', 'V1\Bank::postUpdateBankAccount');
});
