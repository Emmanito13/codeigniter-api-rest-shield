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

$routes->group("api/employee/", ['namespace' => 'App\Controllers\Api\Employee'], function ($routes){
    
    // POST
    $routes->post("create-employee","EmployeeController::addEmployee", ["filter" => "accessauth"]);    
    
});

$routes->group("api/department/", ['namespace' => 'App\Controllers\Api\Department'], function ($routes){           

    // POST
    $routes->post("create-department","DepartmentController::addDepartment", ["filter" => "accessauth"]);    

    // GET
    $routes->get("list-department","DepartmentController::listDepartments", ["filter" => "accessauth"]);    

    // PUT
    $routes->put("update-department/(:num)","DepartmentController::updateDepartment/$1", ["filter" => "accessauth"]);

    // DELETE
    $routes->delete("delete-department/(:num)","DepartmentController::deleteDepartment/$1", ["filter" => "accessauth"]);
    
});