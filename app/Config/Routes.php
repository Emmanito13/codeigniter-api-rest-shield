<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

service('auth')->routes($routes);


$routes->group("api/auth/", ['namespace' => 'App\Controllers\Api\Auth'], function ($routes) {

    // Authentication
    $routes->get("invalid-access","AuthController::accessDenied");

     // POST
     $routes->post("register-user","AuthController::register");

     // POST
     $routes->post("login","AuthController::login");
 
     // GET    
     $routes->get("profile","AuthController::profile", ["filter" => "accessauth"]);
 
     // GET
     $routes->get("logout","AuthController::logout", ["filter" => "accessauth"]);     
});