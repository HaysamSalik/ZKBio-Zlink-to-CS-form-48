<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::login');
$routes->post('/auth', 'Home::auth');

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('/',                 'Home::index');
    $routes->post('logout',           'Home::logout');
    $routes->post('download-pdf',     'Home::download_pdf');
    $routes->post('make-fill-pdf',    'Home::fill');
    $routes->post('import-csv',       'Home::import');
    $routes->post('fetch-id',         'Home::fetch_id');
    $routes->post('fetch-year',       'Home::fetch_year');
    $routes->post('fetch-month',      'Home::fetch_month');
    $routes->post('remove-pdf',       'Home::clearTemp');
});
