<?php
require "../bootstrap.php";
use Src\Controller\PersonController;
use Src\Controller\UserController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Return OK response for preflight requests
    header("HTTP/1.1 200 OK");
    exit();
}

// Determine which controller to use based on the URL
if ($uri[1] === 'person') {
    // The user id is optional for the person endpoint
    $userId = isset($uri[2]) ? (int) $uri[2] : null;
    $requestMethod = $_SERVER["REQUEST_METHOD"];
    
    // Pass the request method and user ID to the PersonController
    $controller = new PersonController($dbConnection, $requestMethod, $userId);
    $controller->processRequest();
    
} elseif ($uri[1] === 'user') {
    // Handle user registration or login (no user ID needed here)
    $requestMethod = $_SERVER["REQUEST_METHOD"];
    
    // Pass the request method to the UserController
    $controller = new UserController($dbConnection, $requestMethod);
    $controller->processRequest();
    
} else {
    header("HTTP/1.1 404 Not Found");
    exit();
}



// require "../bootstrap.php";
// use Src\Controller\PersonController;

// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
// header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// $uri = explode( '/', $uri );

// // all of our endpoints start with /person
// // everything else results in a 404 Not Found
// if ($uri[1] !== 'person') {
//     header("HTTP/1.1 404 Not Found");
//     exit();
// }

// // the user id is, of course, optional and must be a number:
// $userId = null;
// if (isset($uri[2])) {
//     $userId = (int) $uri[2];
// }

// $requestMethod = $_SERVER["REQUEST_METHOD"];

// // pass the request method and user ID to the PersonController and process the HTTP request:
// $controller = new PersonController($dbConnection, $requestMethod, $userId);
// $controller->processRequest();