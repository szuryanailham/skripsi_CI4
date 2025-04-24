<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');


// Define RESTful API routes
$routes->group('api', function ($routes) {
    $routes->get('home/events', 'Home::events');
    
    // Auth Routes (Tanpa Filter)
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/logout', 'AuthController::logout');

    // Resource Routes
    $routes->resource('orders', [
        'controller' => 'OrderController',
        'namespace'  => 'App\Controllers',
 // Tambahkan otentikasi untuk semua order
    ]);
        
    $routes->resource('users', [
        'controller' => 'UsersController',
        'namespace'  => 'App\Controllers',

    ]);
    
    $routes->resource('events', [
        'controller' => 'EventController',
        'namespace'  => 'App\Controllers',
    ]);

    // User Routes (Tanpa Filter)
    $routes->get('user/profile', 'UserController::profile');
    $routes->get('user', 'UserController::index');

    // Custom Order Routes (Dengan Filter)
    $routes->get('orders/(:num)/invoice', 'OrderController::downloadInvoice/$1', ['filter' => 'otentikasi']);
    $routes->put('orders/(:num)/verify', 'OrderController::verifyOrder', ['filter' => 'otentikasi']);
    $routes->get('order/donwload/(:num)', 'OrderController::downloadInvoice/$1');

});
