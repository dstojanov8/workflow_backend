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
}