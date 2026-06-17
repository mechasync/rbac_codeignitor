<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Root URL redirects to dashboard (which is protected and will force login if unauthorized)
$routes->get('/', function() {
    return redirect()->to('/dashboard');
});

// Auth Routes
$routes->match(['get', 'post'], 'login', 'Auth::login');
$routes->match(['get', 'post'], 'register', 'Auth::register');
$routes->match(['get', 'post'], 'reset', 'Auth::reset');
$routes->get('logout', 'Auth::logout');

// Protected Dashboard Routes
$routes->get('dashboard', 'Dashboard::index');

// Users
$routes->get('dashboard/users', 'Dashboard::users');
$routes->post('dashboard/users/save', 'Dashboard::saveUser');
$routes->get('dashboard/users/delete/(:num)', 'Dashboard::deleteUser/$1');

// Roles
$routes->get('dashboard/roles', 'Dashboard::roles');
$routes->post('dashboard/roles/save', 'Dashboard::saveRole');
$routes->get('dashboard/roles/delete/(:any)', 'Dashboard::deleteRole/$1');

// Permissions
$routes->get('dashboard/permissions', 'Dashboard::permissions');
$routes->post('dashboard/permissions/save', 'Dashboard::savePermission');
$routes->get('dashboard/permissions/delete/(:any)', 'Dashboard::deletePermission/$1');
