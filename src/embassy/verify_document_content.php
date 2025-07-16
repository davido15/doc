<?php
session_start();

// Check if user is logged in and has embassy access
if (!isset($_SESSION['user_id']) || $_SESSION['organization_type'] !== 'Embassy')  {
    header("Location: ../login.php");
    exit();
}

require_once '../functions/db.php';
require_once '../functions/config.php';
require_once '../../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to decrypt file content
function decryptFile($encryptedData, $key) {
    $data = json_decode(base64_decode($encryptedData), true);
    if (!$data || !isset($data['iv']) || !isset($data['content'])) {
        throw new Exception('Invalid encrypted data format');
    }

    $iv = base64_decode($data['iv']);
    $content = base64_decode($data['content']);

    $decrypted = openssl_decrypt(
        $content,
        'AES-256-CBC',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );

    if ($decrypted === false) {
        throw new Exception('Decryption failed');
    }

    return $decrypted;
}

// Function to generate hash from file content
function generateFileHash($content) {
    return hash('sha256', $content);
}

// Function to verify file integrity
function verifyFileIntegrity($decryptedContent, $storedHash) {
    $computedHash = generateFileHash($decryptedContent);
    return [
        'isValid' => ($computedHash === $storedHash),
        'storedHash' => $storedHash,
        'computedHash' => $computedHash
    ];
}

// Debug: Log session and POST data
error_log('Session started: ' . (session_status() === PHP_SESSION_ACTIVE ? 'yes' : 'no'));
error_log('POST: ' . print_r($_POST, true));
error_log('SESSION: ' . print_r($_SESSION, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<pre>' . print_r($_POST, true) . '</pre>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['verify_content'])) {
    $document_id = isset($_GET['document_id']) ? intval($_GET['document_id']) : 0;
    error_log('document_id: ' . $document_id);
    
    if (!$document_id) {
        header("Location: view_doc.php?id=" . $document_id . "&error=" . urlencode("Document ID is required"));
        exit();
    }

    try {
        // Get document details
        $stmt = $mysqli->prepare("SELECT * FROM uploads WHERE id = ? AND embassy_id = ?");
        $stmt->bind_param("ii", $document_id, $_SESSION['organization_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header("Location: view_doc.php?id=" . $document_id . "&error=" . urlencode("Document not found or access denied"));
            exit();
        }

        $document = $result->fetch_assoc();

        // Get encryption key
        $encryptionKey = $_ENV['FILE_ENCRYPTION_KEY'] ?? null;
        if (!$encryptionKey) {
            header("Location: view_doc.php?id=" . $document_id . "&error=" . urlencode("Encryption key not configured"));
            exit();
        }

        // Initialize AWS S3 client
        $s3 = new S3Client([
            'region'  => 'eu-north-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'] ?? null,
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? null,
            ],
        ]);

        $bucketName = 'pdfreceiverout';
        $fileKey = $document['file_url'];

        // Get the encrypted file from S3
        $result = $s3->getObject([
            'Bucket' => $bucketName,
            'Key'    => $fileKey
        ]);

        // Decrypt the file content
        $decryptedContent = decryptFile($result['Body'], $encryptionKey);

        // Verify file integrity
        $integrityCheck = verifyFileIntegrity($decryptedContent, $document['file_hash']);

        // Log the verification attempt
        $verificationType = $integrityCheck['isValid'] ? 'integrity_pass' : 'integrity_fail';
        $logStmt = $mysqli->prepare("INSERT INTO document_verifications (document_id, email, action, timestamp, digital_signature, salt) VALUES (?, ?, ?, UNIX_TIMESTAMP(), ?, ?)");
        if ($logStmt) {
            $storedHash = $integrityCheck['storedHash'];
            $computedHash = $integrityCheck['computedHash'];
            $logStmt->bind_param("issss", $document_id, $_SESSION['email'], $verificationType, $storedHash, $computedHash);
            $result = $logStmt->execute();
            
            // Debug: Log the verification attempt
            error_log("Integrity verification logged - Document ID: $document_id, Type: $verificationType, Result: $signature, Success: " . ($result ? 'true' : 'false'));
            
            if (!$result) {
                error_log("Integrity verification insert failed: " . $logStmt->error);
            }
        } else {
            error_log("Failed to prepare integrity verification log statement: " . $mysqli->error);
        }

        // Store verification result in session for display
        $_SESSION['verification_result'] = [
            'success' => true,
            'integrity_check' => $integrityCheck,
            'document_id' => $document_id,
            'verification_time' => date('Y-m-d H:i:s')
        ];
        error_log('Set verification_result in session: ' . print_r($_SESSION['verification_result'], true));
        // Redirect back to view document page
        error_log('Redirecting to: view_doc.php?id=' . $document_id);
        header("Location: view_doc.php?id=" . $document_id);
        exit();

    } catch (Exception $e) {
        header("Location: view_doc.php?id=" . $document_id . "&error=" . urlencode("Verification failed: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?> 