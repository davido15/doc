<?php
// src/s3upload/db.php - Database configuration for upload functionality

// Direct database credentials for upload

$host = $_ENV['DB_HOST'] ;
$username = $_ENV['DB_USER'] ;
$password = $_ENV['DB_PASS'] ;
$database = $_ENV['DB_NAME'] ;





// Connect to the database
$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    error_log("Upload database connection failed: " . $mysqli->connect_error);
    die("Upload connection failed: " . $mysqli->connect_error);
}
?> 
