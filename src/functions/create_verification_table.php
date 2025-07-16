<?php
require_once 'db.php';

// Drop existing table if it exists
$mysqli->query("DROP TABLE IF EXISTS document_verifications");

// Create document_verifications table with updated structure
$sql = "CREATE TABLE document_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    action ENUM('upload', 'download') NOT NULL,
    timestamp INT NOT NULL,
    digital_signature VARCHAR(255) NOT NULL,
    FOREIGN KEY (document_id) REFERENCES uploads(id) ON DELETE CASCADE
)";

if ($mysqli->query($sql)) {
    echo "Table document_verifications created successfully\n";
} else {
    echo "Error creating table: " . $mysqli->error . "\n";
}

// Verify table exists
$result = $mysqli->query("SHOW TABLES LIKE 'document_verifications'");
if ($result->num_rows > 0) {
    echo "Table exists and is ready for data\n";
} else {
    echo "Table was not created successfully\n";
}
?> 