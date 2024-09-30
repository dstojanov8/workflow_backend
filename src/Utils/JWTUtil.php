<?php

namespace Src\Utils;

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key; 

class JWTUtil {
    private static $secret_key = 'f4438319e6250c07918a1391642a70db795b2f2809c0ede01c914e58199f98d1'; // Replace this with your own secret key
    private static $algorithm = 'HS256'; // Algorithm to sign the token

    public static function generateToken($userData) {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;  // jwt valid for 1 hour
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => $userData // user data like ID, email, etc.
        ];

        return JWT::encode($payload, self::$secret_key, self::$algorithm);
    }

    public static function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, self::$algorithm));
            return (array) $decoded->data; // return user data if valid
        } catch (\Exception $e) {
            return false; // token is invalid
        }
    }

    public static function isLoggedIn() {
        // Check if the auth_token cookie is set
        if (isset($_COOKIE['auth_token'])) {
            $authToken = $_COOKIE['auth_token'];
            
            // Validate the token (you may need to use a JWT library)
            $decoded = JWTUtil::validateToken($authToken); // Function to decode and validate JWT
    
            // If the token is valid, return true, else return false
            if ($decoded) {
                return true; // User is logged in
            }
        }
        return false; // User is not logged in
    }
}