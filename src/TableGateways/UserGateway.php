<?php
namespace Src\TableGateways;

class UserGateway {

    private $db = null;

    public function __construct($db) {
        $this->db = $db;
    }

    // Insert new user into the database
    public function insertUser($email, $username, $hashedPassword, $firstname, $lastname) {
        $stmt = $this->db->prepare('INSERT INTO account (email, username, password, firstname, lastname) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$email, $username, $hashedPassword, $firstname, $lastname]);
    }

    // Fetch user by email or username
    public function findUserByEmailOrUsername($emailOrUsername) {
        $stmt = $this->db->prepare('SELECT * FROM account WHERE email = ? OR username = ?');
        $stmt->execute([$emailOrUsername, $emailOrUsername]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findUser($id)
    {
        $statement = "
            SELECT
                id, firstname, lastname, email, username
            FROM
                account
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function updateUser($id, Array $input)
    {
        $statement = "
            UPDATE account
            SET 
                email = :email,
                firstname = :firstname,
                lastname = :lastname,
                username = :username
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'username' => $input['username'],
                'email' => $input['email'],
            ));
            return ['success' => true, 'rowCount' => $statement->rowCount()];
        } catch (\PDOException $e) {
            // Check for duplicate entry error (SQLSTATE 23000) with a specific message
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['success' => false, 'error' => 'Duplicate email entry'];
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}