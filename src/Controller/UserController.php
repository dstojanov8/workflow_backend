<?php
namespace Src\Controller;

use Src\TableGateways\UserGateway;
use Src\Utils\JWTUtil;

class UserController {

    private $db;
    private $requestMethod;
    private $action;

    private $userGateway;

    public function __construct($db, $requestMethod, $action) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->action = $action;

        $this->userGateway = new UserGateway($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->action === 'auth-check'){
                    $this->authenticate();
                } 
                break;
            case 'POST':
                if ($this->action === 'register') {
                    $this->registerUser();
                } elseif ($this->action === 'login') {
                    $this->loginUser();
                } else {
                    $this->invalidRequest();
                }
                break;
            default:
                $this->invalidRequest();
                break;
        }
    }

    private function registerUser() {
        // $email = $_POST['email'];
        // $username = $_POST['username'];
        // $password = $_POST['password'];
        // $firstname = $_POST['firstname'];
        // $lastname = $_POST['lastname'];
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        
        // Validate input
        if (!isset($input['email'], $input['password'], $input['username'])) {
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        // Check if user already exists
        if ($this->userGateway->findUserByEmailOrUsername($input['email']) || $this->userGateway->findUserByEmailOrUsername($input['username'])) {
            echo json_encode(['error' => 'User already exists']);
            return;
        }
        
        // Hash the password and save the user using UserGateway
        $hashedPassword = password_hash($input['password'], PASSWORD_BCRYPT);
        $result = $this->userGateway->insertUser($input['email'], $input['username'], $hashedPassword, $input['firstname'], $input['lastname']);
        
        if ($result) {
            echo json_encode(['success' => 'User registered successfully']);
        } else {
            echo json_encode(['error' => 'User registration failed']);
        }
    }

    // User login with JWT token
    private function loginUser() {
        // $usernameOrEmail = $_POST['usernameOrEmail'];
        // $password = $_POST['password'];
        $input = (array) json_decode(file_get_contents('php://input'), true);

        if (!isset($input['usernameOrEmail'], $input['password'])) {
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $user = $this->userGateway->findUserByEmailOrUsername($input['usernameOrEmail']);
    
        if ($user && password_verify($input['password'], $user['password'])) {
            //* Before we did: Session start
            // session_start();
            // $_SESSION['user_id'] = $user['id'];
            // echo json_encode(['success' => 'Login successful', 'user' => $user]);

            //* Here we generate JWT token
            $jwt = JWTUtil::generateToken([
                'id' => $user['id'],
                'email' => $user['email'],
                'username' => $user['username']
            ]);

            // Send JWT token in an HttpOnly cookie
            $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
            // setcookie('auth_token', $jwt, time() + 3600, "/", "", $isSecure, true);// httpOnly cookie
            setcookie('auth_token', $jwt, [
                'expires' => time() + 3600, // 1 hour from now
                'path' => '/', // Accessible throughout the site
                'domain' => '', // Use the current domain
                'secure' => false, // Set to true when using HTTPS
                'httponly' => true, // Prevent JavaScript access
                'samesite' => 'Lax', // Set SameSite to Lax
            ]);

            echo json_encode(['success' => 'Login successful']);
            // return json_encode(['message' => 'Login successful']);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            // return json_encode(['message' => 'Invalid credentials']);
        }
    }

    private function authenticate() {
        // Get the JWT token from the cookie
        $token = $_COOKIE['auth_token'] ?? null;

        if ($token) {
            $userData = JWTUtil::validateToken($token);
            if ($userData) {
                return json_encode(['message' => 'Authenticated', 'user' => $userData]);
            }
        }

        http_response_code(401);
        return json_encode(['message' => 'Unauthorized']);
    }

    private function invalidRequest() {
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(['error' => 'Invalid request method']);
    }

    //* ILI OVO - PROVERITI
    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}