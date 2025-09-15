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
    $routes->post('crypto-deposit-update', 'Payment::postCrypto_deposit_update');
    $routes->post('crypto-balance-db-check', 'Payment::postCrypto_balance_db_check');

    $routes->post('us-bank', 'V1\Bank::createUsBank');
    $routes->post('us-bank-update', 'V1\Bank::postUpdateUsBankAccount');
    $routes->get('us-bank', 'V1\Bank::getUsBankAccount');

    $routes->post('international-bank', 'V1\Bank::createInternationalBank');
    $routes->post('international-bank-update', 'V1\Bank::postUpdateInternationalBankAccount');
    $routes->get('international-bank', 'V1\Bank::getInternationalBankAccount');
});

$routes->group('calculator', static function ($routes) {
    //mediation
    $routes->get('mediation', 'V1\Calculator::getMediationCalculator');
    $routes->post('mediation', 'V1\Calculator::postCreateMediationCalculator');
    $routes->post('mediation/(:num)', 'V1\Calculator::postUpdateMediationCalculator/$1');
    $routes->delete('mediation/(:num)', 'V1\Calculator::deleteMediationCalculator/$1');
    //otc
    $routes->get('otc', 'V1\Calculator::getOtcCalculator');
    $routes->post('otc', 'V1\Calculator::postCreateOtcCalculator');
    $routes->post('otc/(:num)', 'V1\Calculator::postUpdateOtcCalculator/$1');
    $routes->delete('otc/(:num)', 'V1\Calculator::deleteOtcCalculator/$1');
    //interest
    $routes->get('interest', 'V1\Calculator::getInterestCalculator');
    $routes->post('interest', 'V1\Calculator::postCreateInterestCalculator');
    $routes->post('interest/(:num)', 'V1\Calculator::postUpdateInterestCalculator/$1');
    $routes->delete('interest/(:num)', 'V1\Calculator::deleteInterestCalculator/$1');
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
});
