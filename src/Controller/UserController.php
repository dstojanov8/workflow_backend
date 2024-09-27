<?php
namespace Src\Controller;

use Src\TableGateways\UserGateway;

class UserController {

    private $db;
    private $requestMethod;

    private $userGateway;

    public function __construct($db, $requestMethod) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;

        $this->userGateway = new UserGateway($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'POST':
                $action = $_GET['action'] ?? '';  // Fetch action from query params
                if ($action === 'register') {
                    $this->registerUser();
                } elseif ($action === 'login') {
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
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        
        // Validate input
        if (!isset($input['email'], $input['password'], $input['username'])) {
            echo json_encode(['error' => 'Missing required fields']);
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

    // Register a new user
    // public function registerUser() {
    //     $email = $_POST['email'];
    //     $username = $_POST['username'];
    //     $password = $_POST['password'];
    //     $firstname = $_POST['firstname'];
    //     $lastname = $_POST['lastname'];

    //     // Check if user already exists
    //     if ($this->userGateway->findUserByEmailOrUsername($email) || $this->userGateway->findUserByEmailOrUsername($username)) {
    //         echo json_encode(['error' => 'User already exists']);
    //         return;
    //     }

    //     // Hash password
    //     $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    //     // Insert user into the database
    //     if ($this->userGateway->insertUser($email, $username, $hashedPassword, $firstname, $lastname)) {
    //         echo json_encode(['success' => 'User registered successfully']);
    //     } else {
    //         echo json_encode(['error' => 'Failed to register user']);
    //     }
    // }

    // Handle user login
    public function loginUser() {
        $usernameOrEmail = $_POST['usernameOrEmail'];
        $password = $_POST['password'];

        // Fetch user by email or username
        $user = $this->userGateway->findUserByEmailOrUsername($usernameOrEmail);

        if ($user && password_verify($password, $user['password'])) {
            // Start session or generate token
            session_start();
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(['success' => 'Login successful', 'user' => $user]);
        } else {
            echo json_encode(['error' => 'Invalid credentials']);
        }
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