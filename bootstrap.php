<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

use Src\System\DatabaseConnector;

$dotenv = DotEnv::createImmutable(__DIR__);
$dotenv->load();

// Check if .env file exists
// if (file_exists(__DIR__ . '/.env')) {
//     $dotenv = Dotenv::createImmutable(__DIR__);
//     $dotenv->load();
// } else {
//     echo "<p>.env file does not exist</p>";
// }

$dbConnection = (new DatabaseConnector())->getConnection();
