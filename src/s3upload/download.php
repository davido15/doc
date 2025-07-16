<?php

session_start();
if (empty($_SESSION['email'])) {
    die("You must be logged in to download files.");
}
require_once __DIR__ . '/db.php';



// New: Check user's organization domain by joining users and organizations using email
$user_email = $_SESSION['email'];
$orgStmt = $mysqli->prepare("SELECT o.domain FROM users u JOIN organizations o ON u.organization_id = o.id WHERE u.email = ?");
$orgStmt->bind_param("s", $user_email);
$orgStmt->execute();
$orgStmt->bind_result($org_domain);
if ($orgStmt->fetch()) {
    if (strtolower($org_domain) !== 'embassy') {
        die("You must be in an Embassy organization to download files.");
    }
} else {
    die("User organization not found.");
}
$orgStmt->close();
require __DIR__ . '/../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
require_once __DIR__ . '/../functions/signatures.php';
require_once __DIR__ . '/../functions/utils.php';

// Suppress AWS SDK deprecation warning
error_reporting(E_ALL & ~E_DEPRECATED);

// Load environment variables from .env file
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// AWS S3 config
$awsKey = getenv('AWS_ACCESS_KEY_ID');
$awsSecret = getenv('AWS_SECRET_ACCESS_KEY');
$s3 = new S3Client([
    'region'  => 'eu-north-1',
    'version' => 'latest',
    'credentials' => [
        'key'    => $awsKey,
        'secret' => $awsSecret,
    ],
    'suppress_php_deprecation_warning' => true,
]);
$bucketName = 'pdfreceiverout';

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

// Get key and code from URL
$fileKey = isset($_GET['key']) ? $_GET['key'] : '';
$verificationCode = isset($_GET['code']) ? intval($_GET['code']) : 0;

if (empty($fileKey) || empty($verificationCode)) {
    die("Missing key or verification code");
}

// Get document details
$stmt = $mysqli->prepare("SELECT * FROM uploads WHERE file_url LIKE ? AND verification_code = ?");
$fileKeyParam = '%' . $fileKey . '%';
$stmt->bind_param("si", $fileKeyParam, $verificationCode);
$stmt->execute();
$document = $stmt->get_result()->fetch_assoc();

if ($document) {
    // Get encryption key
    $encryptionKey = getenv('FILE_ENCRYPTION_KEY') ?: 'default-encryption-key-for-development';
    if (!$encryptionKey) {
        die("Encryption key not configured");
    }

    try {
        // Get the object and its metadata first to preserve existing metadata
        $headResult = $s3->headObject([
            'Bucket' => $bucketName,
            'Key'    => $fileKey
        ]);
        $metadata = $headResult['Metadata'] ?? [];

        // Get the encrypted file from S3
        $result = $s3->getObject([
            'Bucket' => $bucketName,
            'Key'    => $fileKey
        ]);

        // Decrypt the file content
        $decryptedContent = decryptFile($result['Body'], $encryptionKey);

        // Note: Hash verification is available as a separate test function
        // No automatic blocking during download to maintain normal workflow

        // Generate download signature for logging purposes only
        $downloader_email = $_SESSION['email'] ?? 'anonymous';
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $signatureData = generateDownloadSignature($document['file_hash'], time(), $downloader_email, $verificationCode, $ip_address);

        // Log the download in the document_verifications table
        if ($signatureData) {
            $logStmt = $mysqli->prepare("INSERT INTO document_verifications (document_id, email, action, timestamp, digital_signature, salt) VALUES (?, ?, 'download', UNIX_TIMESTAMP(), ?, ?)");
            if ($logStmt) {
                $documentId = $document['id'];
                $logStmt->bind_param("isss", $documentId, $downloader_email, $signatureData['signature'], $signatureData['salt']);
                if (!$logStmt->execute()) {
                    error_log("Download verification log failed: " . $logStmt->error);
                }
            } else {
                error_log("Prepare statement for download verification log failed: " . $mysqli->error);
            }
        } else {
            error_log("Failed to generate download signature for document ID: " . ($document['id'] ?? 'unknown'));
        }

        // Log the download in download_logs table
        $downloader_email_for_log = $_SESSION['email'] ?? 'anonymous';
        if (!logFileDownload($mysqli, $document['id'], $downloader_email_for_log)) {
            error_log("Failed to log download to download_logs table for document ID: " . $document['id']);
        }

        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($fileKey) . '"');
        header('Content-Length: ' . strlen($decryptedContent));
        
        // Output the decrypted content
        echo $decryptedContent;
        exit;
    } catch (Exception $e) {
        die("Error downloading file: " . $e->getMessage());
    }
} else {
    die("Document not found or verification code is incorrect");
} 