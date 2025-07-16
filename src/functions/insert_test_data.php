<?php
require_once 'db.php';

// Function to generate random email
function generateRandomEmail() {
    $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
    $name = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8);
    $domain = $domains[array_rand($domains)];
    return $name . '@' . $domain;
}

// Function to generate a random digital signature
function generateDigitalSignature() {
    return bin2hex(random_bytes(32)); // 64 character hex string
}

// Get all document IDs from uploads table
$stmt = $mysqli->prepare("SELECT id FROM uploads");
if (!$stmt) {
    die("Error preparing statement: " . $mysqli->error);
}

if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}

$result = $stmt->get_result();
$documentIds = [];
while ($row = $result->fetch_assoc()) {
    $documentIds[] = $row['id'];
}

echo "Found " . count($documentIds) . " documents in uploads table\n";

if (empty($documentIds)) {
    die("No documents found in uploads table. Please upload some documents first.");
}

// Insert test data for each document
foreach ($documentIds as $documentId) {
    echo "Processing document ID: " . $documentId . "\n";
    
    // Insert upload record
    $uploadEmail = generateRandomEmail();
    $uploadTime = time() - rand(86400, 2592000); // Random time between 1 day and 30 days ago
    $uploadSignature = generateDigitalSignature();
    
    $stmt = $mysqli->prepare("INSERT INTO document_verifications (document_id, email, action, timestamp, digital_signature) VALUES (?, ?, 'upload', ?, ?)");
    if (!$stmt) {
        echo "Error preparing upload statement: " . $mysqli->error . "\n";
        continue;
    }
    
    if (!$stmt->bind_param("isis", $documentId, $uploadEmail, $uploadTime, $uploadSignature)) {
        echo "Error binding upload parameters: " . $stmt->error . "\n";
        continue;
    }
    
    if (!$stmt->execute()) {
        echo "Error executing upload statement: " . $stmt->error . "\n";
        continue;
    }
    
    echo "Inserted upload record for document " . $documentId . "\n";
    
    // Insert 1-3 download records
    $downloadCount = rand(1, 3);
    for ($i = 0; $i < $downloadCount; $i++) {
        $downloadEmail = generateRandomEmail();
        $downloadTime = $uploadTime + rand(3600, 86400); // Random time between 1 hour and 1 day after upload
        $downloadSignature = generateDigitalSignature();
        
        $stmt = $mysqli->prepare("INSERT INTO document_verifications (document_id, email, action, timestamp, digital_signature) VALUES (?, ?, 'download', ?, ?)");
        if (!$stmt) {
            echo "Error preparing download statement: " . $mysqli->error . "\n";
            continue;
        }
        
        if (!$stmt->bind_param("isis", $documentId, $downloadEmail, $downloadTime, $downloadSignature)) {
            echo "Error binding download parameters: " . $stmt->error . "\n";
            continue;
        }
        
        if (!$stmt->execute()) {
            echo "Error executing download statement: " . $stmt->error . "\n";
            continue;
        }
        
        echo "Inserted download record " . ($i + 1) . " for document " . $documentId . "\n";
    }
}

// Verify the data was inserted
$stmt = $mysqli->prepare("SELECT document_id, action, COUNT(*) as count FROM document_verifications GROUP BY document_id, action");
if (!$stmt) {
    die("Error preparing verification statement: " . $mysqli->error);
}

if (!$stmt->execute()) {
    die("Error executing verification statement: " . $stmt->error);
}

$result = $stmt->get_result();
echo "\nVerification of inserted data:\n";
while ($row = $result->fetch_assoc()) {
    echo "Document " . $row['document_id'] . " has " . $row['count'] . " " . $row['action'] . " records\n";
}

// Check if the document ID exists in the uploads table
$stmt = $mysqli->prepare("SELECT id FROM uploads WHERE id = ?");
if ($stmt) {
    $documentId = 1; // Replace with an actual document ID
    $stmt->bind_param("i", $documentId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Insert dummy data for document verifications
        $stmt = $mysqli->prepare("INSERT INTO document_verifications (document_id, email, action, timestamp) VALUES (?, ?, 'upload', UNIX_TIMESTAMP())");
        if ($stmt) {
            $email = 'test@example.com';
            $stmt->bind_param("is", $documentId, $email);
            $stmt->execute();
        } else {
            die("Error preparing statement: " . $mysqli->error);
        }

        // Insert a download verification
        $stmt = $mysqli->prepare("INSERT INTO document_verifications (document_id, email, action, timestamp) VALUES (?, ?, 'download', UNIX_TIMESTAMP())");
        if ($stmt) {
            $stmt->bind_param("is", $documentId, $email);
            $stmt->execute();
        } else {
            die("Error preparing statement: " . $mysqli->error);
        }
    } else {
        die("Document ID does not exist in the uploads table");
    }
} else {
    die("Error preparing statement: " . $mysqli->error);
}
?> 