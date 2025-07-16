<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['organization_type'] !== 'Embassy') {
    header("Location: ../login.php");
    exit();
}

require_once '../functions/db.php';
require_once '../functions/signatures.php';
require_once '../../vendor/autoload.php';

// Get document ID from URL
$doc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Initialize S3 client
$s3 = new Aws\S3\S3Client([
    'region'  => 'eu-north-1',
    'version' => 'latest',
    'credentials' => [
        'key'    => getenv('AWS_ACCESS_KEY_ID') ?: 'test',
        'secret' => getenv('AWS_SECRET_ACCESS_KEY') ?: 'test',
    ],
    'suppress_php_deprecation_warning' => true,
]);
$bucketName = 'pdfreceiverout';

// Perform the lifecycle verification
$lifecycleVerification = verifyDocumentLifecycle($mysqli, $doc_id, $s3, $bucketName); 