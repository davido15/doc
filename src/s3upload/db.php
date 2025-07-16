<?php
// db.php

// Load environment type from .env file using dotenv
require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
    $dotenv->load();
} catch (Exception $e) {
    // .env file not found, continue with defaults
}

// Get environment type from .env file, default to 'local'
$env_type = $_ENV['ENV_TYPE'] ;
$db_host = $_ENV['DB_HOST'] ;
$db_username = $_ENV['DB_USER'] ;
$db_password = $_ENV['DB_PASS'] ;
$db_name = $_ENV['DB_NAME'] ;



// Use appropriate credentials based on environment type
if ($env_type === 'production') {
    // Production database credentials
    $host = $db_host;
    $username = $db_username;
    $password = $db_password;
    $database = $db_name;


} else {
    // Local database credentials
    $host = 'localhost';
    $username = 'root';
    $password = 'root';
    $database = 'pdf_verfier';
}

// Connect to the appropriate database
$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    error_log("Database connection failed: " . $mysqli->connect_error);
    die("Connection failed: " . $mysqli->connect_error);
}

// NOTE: Make sure your users table has an 'is_verified' TINYINT(1) DEFAULT 0 column for email verification.
// NOTE: You also need an 'email_verifications' table with columns: id (auto), email, token, expires_at.
?>
