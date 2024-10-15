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
        $this->action = $action; //* Also used as userId in PUT updateUser

        $this->userGateway = new UserGateway($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->action === 'auth-check'){
                    $response = $this->authenticate();
                } 
                break;
            case 'POST':
                if ($this->action === 'register') {
                    $response = $this->registerUser();
                } elseif ($this->action === 'login') {
                    $response = $this->loginUser();
                } else {
                    $response = $this->notFoundResponse();
                }
                break;
            case 'PUT':
                $response = $this->updateUser($this->action);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
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
            return $this->unprocessableEntityResponse();
        }

        // Check if user already exists
        if ($this->userGateway->findUserByEmailOrUsername($input['email'])) {
            $response['status_code_header'] = 'HTTP/1.1 409 Conflict';
            $response['body'] = json_encode(['message' => 'Email already in use.']);
            return $response;
        }
        // Check if user already exists
        if ($this->userGateway->findUserByEmailOrUsername($input['username'])) {
            $response['status_code_header'] = 'HTTP/1.1 409 Conflict';
            $response['body'] = json_encode(['message' => 'Username already in use.']);
            return $response;
        }
        
        // Hash the password and save the user using UserGateway
        $hashedPassword = password_hash($input['password'], PASSWORD_BCRYPT);
        $result = $this->userGateway->insertUser($input['email'], $input['username'], $hashedPassword, $input['firstname'], $input['lastname']);
        
        if ($result) {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(['message' => 'User registered successfully']);
            return $response;
        } else {
            return $this->unprocessableEntityResponse();
        }
    }

    private function updateUser($id) 
    {
        $result = $this->userGateway->findUser($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        // Validate input
        if (!isset($input['firstname'], $input['lastname'], $input['username'], $input['email'])) {
            return $this->unprocessableEntityResponse();
        }
        $updateResult = $this->userGateway->updateUser($id, $input);
        //* In case email is already in use
        if (!$updateResult['success']) {
            if ($updateResult['error'] === 'Duplicate email entry') {
                $response['status_code_header'] = 'HTTP/1.1 409 Conflict';
                $response['body'] = json_encode(['message' => 'Email already exists.']);
            } else {
                $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
                $response['body'] = json_encode(['message' => 'An error occurred while updating the user.']);
            }
            return $response;
        }

        //* Return updated user
        $userData = $this->userGateway->findUser($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['message' => 'User updated successfully', 'user' => $userData]);
        return $response;
    }

    // User login with JWT token
    private function loginUser() {
        // $usernameOrEmail = $_POST['usernameOrEmail'];
        // $password = $_POST['password'];
        $input = (array) json_decode(file_get_contents('php://input'), true);

        if (!isset($input['usernameOrEmail'], $input['password'])) {
            return $this->unprocessableEntityResponse();
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
                'username' => $user['username'],
                //'exp' => time() + 300 //* Does nothing as generateToken function sets 'exp'
            ]);

            // Send JWT token in an HttpOnly cookie
            $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
            setcookie('auth_token', $jwt, time() + 3600, "/", "", $isSecure, true);// httpOnly cookie

            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(['user' => $user, 'userToken' => $jwt]);
            return $response;
        } else {
            $response['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
            $response['body'] = json_encode(['message' => 'Invalid credentials']);
            return $response;
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

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'message' => 'Invalid input. Missing required fields.'
        ]);
        return $response;
    }

    //* ILI OVO - PROVERITI
    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}