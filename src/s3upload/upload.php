<?php
require __DIR__ . '/../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

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

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions/signatures.php';
require_once __DIR__ . '/../notifications/NotificationHandler.php';

// Suppress AWS SDK deprecation warning
error_reporting(E_ALL & ~E_DEPRECATED);

// Debug: Check if environment variables are loaded
$awsKey = getenv('AWS_ACCESS_KEY_ID');
$awsSecret = getenv('AWS_SECRET_ACCESS_KEY');

if (!$awsKey || !$awsSecret) {
    error_log("AWS credentials not found. Key: " . ($awsKey ? 'SET' : 'NOT SET') . ", Secret: " . ($awsSecret ? 'SET' : 'NOT SET'));
    header("Location: ../bank/upload?error=" . urlencode("AWS credentials not configured properly"));
    exit;
}

// AWS S3 config
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

// Function to encrypt file content
function encryptFile($content, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
    $encrypted = openssl_encrypt(
        $content,
        'AES-256-CBC',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );
    
    if ($encrypted === false) {
        throw new Exception('Encryption failed');
    }
    
    $data = [
        'iv' => base64_encode($iv),
        'content' => base64_encode($encrypted)
    ];
    
    return base64_encode(json_encode($data));
}

// Function to generate hash from file content
function generateFileHash($content) {
    return hash('sha256', $content);
}

session_start();
if (empty($_SESSION['email'])) {
    die("You must be logged in to upload files.");
}

// New: Check user's organization domain by joining users and organizations using email
$user_email = $_SESSION['email'];
$orgStmt = $mysqli->prepare("SELECT o.domain FROM users u JOIN organizations o ON u.organization_id = o.id WHERE u.email = ?");
$orgStmt->bind_param("s", $user_email);
$orgStmt->execute();
$orgStmt->bind_result($org_domain);
if ($orgStmt->fetch()) {
    if (strtolower($org_domain) !== 'bank') {
        die("You must be in a Bank organization to upload files.");
    }
} else {
    die("User organization not found.");
}
$orgStmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phonenumber'] ?? '';
    $name = $_POST['name'] ?? '';
    $requesting_for = $_POST['requesting_for'] ?? '';
    $beneficiary_name = $_POST['beneficiary_name'] ?? '';
    $beneficiary_dob = $_POST['beneficiary_dob'] ?? '';
    $date = $_POST['date'] ?? '';
    $organization_id = $_POST['organization_id'] ?? 0;
    $user_id = $_POST['user_id'] ?? 0;
    $bank_user_email = $_POST['bank_user_email'] ?? $_SESSION['email'] ?? 'bank_user@example.com';

    // Generate a unique verification code
    do {
        $verification_code = random_int(10000000, 99999999);
        $stmt = $mysqli->prepare("SELECT id FROM uploads WHERE verification_code = ?");
        $stmt->bind_param("i", $verification_code);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);

    $fileUrls = [];
    $uploadGroupId = uniqid('grp_', true);
    $files = $_FILES['file'];

    $maxSize = 10 * 1024 * 1024;
    $allowedMime = 'application/pdf';
    $errors = [];
    $successes = [];

    // Get encryption key from environment variable
    $encryptionKey = getenv('FILE_ENCRYPTION_KEY') ?: 'default-encryption-key-for-development';
    if (!$encryptionKey) {
        header("Location: ../bank/upload?error=" . urlencode("Encryption key not configured"));
        exit;
    }

    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === 0) {
            $tmpFile = $files['tmp_name'][$i];
            $originalName = basename($files['name'][$i]);
            $fileType = mime_content_type($tmpFile);
            $fileSize = filesize($tmpFile);

            // Validate file type
            if ($fileType !== $allowedMime) {
                $errors[] = "$originalName is not a PDF.";
                continue;
            }

            // Validate file size
            if ($fileSize > $maxSize) {
                $errors[] = "$originalName exceeds the 10MB limit.";
                continue;
            }

            try {
                // Read file content
                $fileContent = file_get_contents($tmpFile);
                
                // Generate hash from file content
                $fileHash = generateFileHash($fileContent);
                
                // Encrypt the file content
                $encryptedContent = encryptFile($fileContent, $encryptionKey);
                
                // Generate upload signature
                $signatureData = generateUploadSignature($fileHash, time(), $email);
                
                // Create a temporary file with encrypted content
                $encryptedTmpFile = tempnam(sys_get_temp_dir(), 'enc_');
                file_put_contents($encryptedTmpFile, $encryptedContent);
                
                $s3Key = $uploadGroupId . '_' . $originalName;

                // Upload encrypted file to S3
                $result = $s3->putObject([
                    'Bucket' => $bucketName,
                    'Key'    => $s3Key,
                    'SourceFile' => $encryptedTmpFile,
                    'ACL'    => 'private',
                    'Metadata' => [
                        'signature' => $signatureData['signature'],
                        'salt' => $signatureData['salt']
                    ]
                ]);

                // Clean up temporary file
                unlink($encryptedTmpFile);
                
                // Set status and embassy_id based on requesting_for
                if ($requesting_for === 'myself') {
                    $status = 'With All Embassy';
                    $embassy_id = 99; // Default embassy for "myself" requests
                } else {
                    $status = 'With Bank';
                    $embassy_id = null; // Will be set later when manually sent to embassy
                }
                
                // Insert a new record for each file
                $stmt = $mysqli->prepare("INSERT INTO uploads (organization_id, user_id, name, email, phonenumber, requesting_for, beneficiary_name, beneficiary_dob, date, file_url, verification_code, file_hash, Status, embassy_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt === false) throw new Exception("Prepare failed: " . $mysqli->error);
                
                $stmt->bind_param("iisssssssssssi", $organization_id, $user_id, $name, $email, $phone, $requesting_for, $beneficiary_name, $beneficiary_dob, $date, $s3Key, $verification_code, $fileHash, $status, $embassy_id);
                $result = $stmt->execute();
                
                // Debug: Check if insert was successful
                if ($result) {
                    error_log("Upload insert successful. Document ID: " . $mysqli->insert_id);
                } else {
                    error_log("Upload insert failed: " . $stmt->error);
                }

                // Get the inserted document ID
                $documentId = $mysqli->insert_id;

                // Log the upload in document_verifications table
                if ($signatureData) {
                    $logStmt = $mysqli->prepare("INSERT INTO document_verifications (document_id, email, action, timestamp, digital_signature, salt) VALUES (?, ?, 'upload', UNIX_TIMESTAMP(), ?, ?)");
                    if ($logStmt) {
                        $logStmt->bind_param("isss", $documentId, $bank_user_email, $signatureData['signature'], $signatureData['salt']);
                        if (!$logStmt->execute()) {
                            error_log("Upload verification log failed: " . $logStmt->error);
                        }
                    } else {
                        error_log("Prepare statement for upload verification log failed: " . $mysqli->error);
                    }
                } else {
                    error_log("Failed to generate upload signature for document ID: $documentId");
                }

                $successes[] = [
                    'name' => $originalName
                ];

                // Send verification code email to requestor
                try {
                    require_once __DIR__ . '/../notifications/config.php';
                    $notificationHandler = new NotificationHandler($mysqli);
                    $subject = OTP_EMAIL_SUBJECT;
                    $body = str_replace('{OTP}', $verification_code, OTP_EMAIL_TEMPLATE);
                    // Send to user in session
                    $notificationHandler->sendEmail($_SESSION['email'], $subject, $body);
                    // Send to admin
                    $notificationHandler->sendEmail("daviddors12@gmail.com", $subject, $body);
                } catch (Exception $emailError) {
                    error_log("Failed to send verification code email: " . $emailError->getMessage());
                }

            } catch (Exception $e) {
                $errors[] = "Action failed for $originalName: " . $e->getMessage();
            }
        } else {
            $errors[] = $files['name'][$i] . " encountered an upload error.";
        }
    }

    $finalMessage = '';
    if (!empty($successes)) {
        $successDetails = [];
        foreach ($successes as $s) {
            $successDetails[] = $s['name'];
        }
        $finalMessage .= "Successfully uploaded " . count($successes) . " file(s): " . implode('; ', $successDetails) . ". Verification Code: " . $verification_code;
        
        // Add status information based on requesting_for
        if ($requesting_for === 'myself') {
            $finalMessage .= " (Automatically sent to Embassy)";
        } else {
            $finalMessage .= " (Ready to send to Embassy)";
        }
    }
    if (!empty($errors)) {
        $finalMessage .= " Errors: " . implode(', ', $errors);
    }
    
    if (!empty($successes)) {
        header("Location: ../bank/upload?success=" . urlencode($finalMessage));
    } else {
        header("Location: ../bank/upload?error=" . urlencode($finalMessage));
    }
    exit;
}
?>