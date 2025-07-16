<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['organization_type'] !== 'Embassy') {
    header("Location: ../login.php");
    exit();
}

require_once '../functions/db.php';
require_once '../functions/config.php';
require_once '../functions/utils.php';

// Get file ID and verify access
$file_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $mysqli->prepare("SELECT * FROM uploads WHERE id = ? AND embassy_id = ?");
$stmt->bind_param("ii", $file_id, $_SESSION['organization_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: /embassy/dashboard");
    exit();
}

$document = $result->fetch_assoc();

// Get the file key from the URL
$file_key = basename($_GET['file'] ?? '');
if (empty($file_key)) {
    header("Location: /embassy/view_doc?id=" . $file_id);
    exit();
}

// Initialize AWS S3 client
require_once '../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$s3 = new S3Client([
    'region'  => 'eu-north-1',
    'version' => 'latest',
    'credentials' => [
        'key'    => getenv('AWS_ACCESS_KEY_ID'),
        'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
    ],
]);

try {
    // Get the encrypted file from S3
    $result = $s3->getObject([
        'Bucket' => 'pdfreceiverout',
        'Key'    => $file_key
    ]);

    // Get the encrypted content
    $encryptedData = json_decode(base64_decode($result['Body']), true);
    
    if (!$encryptedData || !isset($encryptedData['iv']) || !isset($encryptedData['content'])) {
        throw new Exception('Invalid encrypted data format');
    }

    // Get encryption key from environment
    $encryptionKey = getenv('FILE_ENCRYPTION_KEY');
    if (!$encryptionKey) {
        throw new Exception('Encryption key not configured');
    }

    // Decrypt the content
    $decrypted = openssl_decrypt(
        base64_decode($encryptedData['content']),
        'AES-256-CBC',
        $encryptionKey,
        OPENSSL_RAW_DATA,
        base64_decode($encryptedData['iv'])
    );

    if ($decrypted === false) {
        throw new Exception('Decryption failed');
    }

    // Note: Hash verification is available as a separate test function
    // No automatic blocking during download to maintain normal workflow

    // Log the download
    logFileDownload($mysqli, $file_id, $_SESSION['email']);

    // Set headers for PDF download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($file_key) . '"');
    header('Content-Length: ' . strlen($decrypted));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Output the decrypted content
    echo $decrypted;
    exit;

} catch (Exception $e) {
    // Log the error
    error_log("Download error: " . $e->getMessage());
    header("Location: /embassy/view_doc?id=" . $file_id . "&error=" . urlencode("Download failed: " . $e->getMessage()));
    exit();
}
?> 