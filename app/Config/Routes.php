<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', function ($routes) {    
    // Auth routes
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');
    $routes->post('logout', 'AuthController::logout');

    // Show all events for guests
    $routes->get('user/events', 'EventController::index');   
     $routes->get('user/events/(:num)', 'EventController::show/$1');

    $routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('events/(:num)', 'EventController::show/$1');
    $routes->post('orders', 'OrderController::create');
    $routes->get('user/list-orders', 'OrderController::listOrders');
    $routes->delete('orders/(:num)', 'OrderController::delete/$1');
    $routes->post('orders/(:num)/upload-proof', 'OrderController::uploadProof/$1');
     $routes->get('ticket/download/(:num)', 'TicketController::download/$1');
    });


$routes->group('admin', ['filter' => 'admin'], function($routes) {
   $routes->resource('events', ['controller' => 'EventController']);
    $routes->get('orders', 'OrderController::index');
   $routes->put('orders/(:num)/verify', 'OrderController::verifyOrder/$1');
});




});
