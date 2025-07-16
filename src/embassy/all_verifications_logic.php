<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['organization_type'] !== 'Embassy') {
    header("Location: ../login.php");
    exit();
}

require_once '../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once '../functions/db.php';
require_once '../functions/signatures.php';

// Initialize S3 client
$s3 = new Aws\S3\S3Client([
    'region'  => 'eu-north-1',
    'version' => 'latest',
    'credentials' => [
        'key'    => $_ENV['AWS_ACCESS_KEY_ID'] ?? null,
        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? null,
    ],
    'suppress_php_deprecation_warning' => true,
]);
$bucketName = 'pdfreceiverout';

// Get document ID from URL
$doc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get document details
$stmt = $mysqli->prepare("SELECT * FROM uploads WHERE id = ? AND organization_id = ?");
$stmt->bind_param("ii", $doc_id, $_SESSION['organization_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$document = $result->fetch_assoc();

// --- Verification Logic ---

// Document Activity Check
$integrity_result = null;
if (isset($_POST['check_integrity'])) {
    $upload_email = $document['email'];
    $upload_time = $document['date'];
    $stmt = $mysqli->prepare("
        SELECT 
            COUNT(*) as download_count,
            MAX(download_time) as last_download,
            GROUP_CONCAT(DISTINCT downloader_email) as download_emails
        FROM download_logs 
        WHERE file_id = ?
    ");
    if ($stmt) {
        $stmt->bind_param("i", $doc_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $downloadData = $result->fetch_assoc();
        $integrity_result = [
            'has_upload' => true,
            'has_download' => $downloadData['download_count'] > 0,
            'upload_count' => 1,
            'download_count' => $downloadData['download_count'],
            'last_upload' => $upload_time,
            'last_download' => $downloadData['last_download'],
            'upload_emails' => [$upload_email],
            'download_emails' => $downloadData['download_emails'] ? explode(',', $downloadData['download_emails']) : []
        ];
    }
}

// Document Content Verification
$isIntegrityMaintained = null;
$debugHashes = [];
if (isset($_POST['verify_integrity'])) {
    $storedHash = $document['file_hash'];
    $debugHashes['stored'] = $storedHash ?: 'Not found in database';

    if ($storedHash && !empty($document['file_url'])) {
        try {
            $fileKey = explode(',', $document['file_url'])[0];
            $result = $s3->getObject(['Bucket' => $bucketName, 'Key' => $fileKey]);
            $fileContent = $result['Body']->getContents();
            $newHash = hash('sha256', $fileContent);
            $debugHashes['new'] = $newHash;
            $isIntegrityMaintained = ($storedHash === $newHash);
        } catch (Exception $e) {
            $isIntegrityMaintained = false;
            $debugHashes['new'] = 'Error fetching file from S3: ' . $e->getMessage();
        }
    } else {
        $isIntegrityMaintained = false;
        if (!$storedHash) $debugHashes['stored'] = 'Hash not found in the database for this document.';
        if (empty($document['file_url'])) $debugHashes['new'] = 'File URL not found for this document.';
    }
}

// Document Lifecycle Verification
$lifecycleVerification = null;
if (isset($_POST['verify_lifecycle'])) {
    $lifecycleVerification = verifyDocumentLifecycle($mysqli, $doc_id, $s3, $bucketName);
} 