<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
  require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Set base API routing path
$baseApiPath = 'api/'.getenv('app.apiVersion');

// Generic routes
$routes->get('/', 'Home::index');
$routes->get($baseApiPath, 'Home::welcome');
$routes->cli($baseApiPath.'/initialize', 'Home::initialize');

// User routes
$routes->post($baseApiPath.'/users', 'Users::add');

// Seller routes
$routes->post($baseApiPath.'/sellers', 'Sellers::add');
$routes->delete($baseApiPath.'/sellers/(:num)', 'Sellers::delete/$1');
$routes->post($baseApiPath.'/sellers/products', 'Sellers::addProduct');
$routes->delete($baseApiPath.'/sellers/products/(:num)', 'Sellers::deleteProduct/$1');

// Cart routes
$routes->post($baseApiPath.'/carts/products', 'CartProducts::add');
$routes->delete($baseApiPath.'/carts/products/(:num)', 'CartProducts::delete/$1');
$routes->delete($baseApiPath.'/carts/users/(:num)', 'CartProducts::deleteAll/$1');
$routes->post($baseApiPath.'/carts/commit', 'CartProducts::commit');
$routes->get($baseApiPath.'/carts/users/(:num)/amount', 'CartProducts::amount/$1');
$routes->patch($baseApiPath.'/carts/increase/products', 'CartProducts::increase');
$routes->patch($baseApiPath.'/carts/decrease/products', 'CartProducts::decrease');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
  require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}