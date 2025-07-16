<?php
// src/s3upload/db.php - Database configuration for upload functionality

// Direct database credentials for upload

$host = $_ENV['DB_HOST'] ?? 'localhost';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? 'root';
$database = $_ENV['DB_NAME'] ?? 'pdf_verifier';




// Connect to the database
$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    error_log("Upload database connection failed: " . $mysqli->connect_error);
    die("Upload connection failed: " . $mysqli->connect_error);
}
?> 
