<?php
require "../bootstrap.php";
use Src\Controller\PersonController;
use Src\Controller\UserController;
use Src\Utils\JWTUtil;

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Return OK response for preflight requests
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("HTTP/1.1 200 OK");
    exit();
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// Determine which controller to use based on the URL
if ($uri[1] === 'user') {
    // Handle user registration or login (no user ID needed here)
    $requestMethod = $_SERVER["REQUEST_METHOD"];
    $action = $uri[2];
    // Pass the request method to the UserController
    $controller = new UserController($dbConnection, $requestMethod, $action);
    $controller->processRequest();
    
} elseif ($uri[1] === 'person') {
    // Get the Authorization header
    $headers = apache_request_headers();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

    // Extract the token (remove "Bearer " prefix)
    $token = null;
    if ($authHeader) {
        list($bearer, $token) = explode(' ', $authHeader);
    }

    // Validate the token if it exists
    $decodedToken = null;
    if ($token) {
        $decodedToken = JWTUtil::validateToken($token);
    }

    // Ensure the user is authenticated for certain routes
    if (!$decodedToken) {
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(['message' => 'Unauthorized']);
        exit();
    }
    
    // The user id is optional for the person endpoint
    $userId = isset($uri[2]) ? (int) $uri[2] : null;
    $requestMethod = $_SERVER["REQUEST_METHOD"];
    
    // Pass the request method and user ID to the PersonController
    $controller = new PersonController($dbConnection, $requestMethod, $userId, $decodedToken);
    $controller->processRequest();
    
} else {
    header("HTTP/1.1 404 Not Found");
    exit();
}
