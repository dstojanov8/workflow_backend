<?php

namespace Utils;

use \Firebase\JWT\JWT;

class JWTUtil {
    private static $secret_key = 'your_secret_key'; // Replace this with your own secret key
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
            $decoded = JWT::decode($token, self::$secret_key, [self::$algorithm]);
            return (array) $decoded->data; // return user data if valid
        } catch (\Exception $e) {
            return false; // token is invalid
        }
    }
}