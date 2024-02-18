<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->post('register', 'UserRegistration::index');
$routes->post('login', 'UserLogin::index');
