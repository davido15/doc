<?php
function loadEnv($path = null) {
    if ($path === null) {
        $path = __DIR__ . '/../../.env';
    }
    
    if (!file_exists($path)) {
        throw new Exception('.env file not found');
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load environment variables using dotenv
require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
    $dotenv->load();
} catch (Exception $e) {
    // .env file not found, continue with defaults
}

// Get environment type from .env file, default to 'local'
$env_type = $_ENV['ENV_TYPE'] ?? 'local';

// Use appropriate credentials based on environment type
if ($env_type === 'production') {
    // Production database credentials
    $host = 'localhost';
    $username = 'root';
    $password = '969sVc+hI.!5';
    $database = 'pdf_verfier';
} else {
    // Local database credentials
    $host = 'localhost';
    $username = 'root';
    $password = 'root';
    $database = 'pdf_verfier';
}

// Initialize MySQLi connection
$mysqli = new mysqli($host, $username, $password, $database);
if ($mysqli->connect_errno) {
    die('Connect Error: ' . $mysqli->connect_error);
} 