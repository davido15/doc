<?php
require_once 'config.php';

/**
 * Generate a document hash and signature
 * @param string $content File content
 * @param string $email User's email
 * @return array Hash and signature data
 */
function generateDocumentVerification($content, $email) {
    // Generate document hash
    $documentHash = hash('sha256', $content);
    
    // Generate timestamp
    $timestamp = time();
    
    // Create verification data
    $verificationData = [
        'document_hash' => $documentHash,
        'email' => $email,
        'timestamp' => $timestamp
    ];
    
    // Generate signature using a secret key
    $secretKey = getenv('DOCUMENT_VERIFICATION_KEY');
    $signature = hash_hmac('sha256', json_encode($verificationData), $secretKey);
    
    return [
        'hash' => $documentHash,
        'signature' => $signature,
        'timestamp' => $timestamp
    ];
}

/**
 * Verify document integrity
 * @param string $content File content
 * @param string $storedHash Stored document hash
 * @param string $storedSignature Stored signature
 * @param string $email User's email
 * @param int $timestamp Original timestamp
 * @return array Verification result
 */
function verifyDocumentIntegrity($content, $storedHash, $storedSignature, $email, $timestamp) {
    // Calculate current hash
    $currentHash = hash('sha256', $content);
    
    // Verify hash matches
    $hashValid = hash_equals($storedHash, $currentHash);
    
    // Recreate verification data
    $verificationData = [
        'document_hash' => $storedHash,
        'email' => $email,
        'timestamp' => $timestamp
    ];
    
    // Verify signature
    $secretKey = getenv('DOCUMENT_VERIFICATION_KEY');
    $expectedSignature = hash_hmac('sha256', json_encode($verificationData), $secretKey);
    $signatureValid = hash_equals($storedSignature, $expectedSignature);
    
    return [
        'success' => true,
        'hash_valid' => $hashValid,
        'signature_valid' => $signatureValid,
        'timestamp' => $timestamp,
        'current_hash' => $currentHash,
        'stored_hash' => $storedHash
    ];
}

/**
 * Generate a digital signature for a document event
 * @param int $documentId Document ID
 * @param string $email User's email
 * @param string $action Action type
 * @param int $timestamp Unix timestamp
 * @return string Digital signature
 */
function generateDigitalSignature($documentId, $email, $action, $timestamp) {
    // Create a unique salt for each record
    $salt = bin2hex(random_bytes(16));
    
    // Combine all data into a single string
    $data = $documentId . '|' . $email . '|' . $action . '|' . $timestamp . '|' . $salt;
    
    // Generate a secure hash using Argon2id (similar to password hashing)
    return [
        'signature' => password_hash($data, PASSWORD_ARGON2ID),
        'salt' => $salt
    ];
}

/**
 * Verify a digital signature
 * @param string $data The original data string
 * @param string $signature The stored signature
 * @return bool Whether the signature is valid
 */
function verifyDigitalSignature($data, $signature) {
    return password_verify($data, $signature);
}

/**
 * Log document verification event
 * @param int $documentId Document ID
 * @param string $email User's email
 * @param string $action Action type (upload/download)
 * @return bool Success status
 */
function logDocumentVerification($documentId, $email, $action) {
    global $mysqli;
    
    try {
        $timestamp = time();
        $signatureData = generateDigitalSignature($documentId, $email, $action, $timestamp);
        
        $stmt = $mysqli->prepare("INSERT INTO document_verifications 
            (document_id, email, action, timestamp, digital_signature, signature_salt) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississ", 
            $documentId, 
            $email, 
            $action, 
            $timestamp, 
            $signatureData['signature'],
            $signatureData['salt']
        );
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Failed to log document verification: " . $e->getMessage());
        return false;
    }
}

/**
 * Verify the integrity of a verification record
 * @param array $record The verification record
 * @return bool Whether the record is valid
 */
function verifyRecordIntegrity($record) {
    $data = $record['document_id'] . '|' . 
            $record['email'] . '|' . 
            $record['action'] . '|' . 
            $record['timestamp'] . '|' . 
            $record['signature_salt'];
            
    return verifyDigitalSignature($data, $record['digital_signature']);
}

/**
 * Check document verification history
 * @param int $documentId Document ID
 * @return array Verification history
 */
function getDocumentVerificationHistory($documentId) {
    global $mysqli;
    
    try {
        // First check if the document exists
        $checkStmt = $mysqli->prepare("SELECT id FROM uploads WHERE id = ?");
        if (!$checkStmt) {
            throw new Exception("Failed to prepare document check query: " . $mysqli->error);
        }
        $checkStmt->bind_param("i", $documentId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows === 0) {
            return [
                'success' => false,
                'error' => 'Document not found'
            ];
        }
        
        // Get verification history
        $stmt = $mysqli->prepare("
            SELECT document_id, email, action, timestamp, digital_signature, signature_salt 
            FROM document_verifications 
            WHERE document_id = ? 
            ORDER BY timestamp ASC
        ");
        
        if (!$stmt) {
            throw new Exception("Failed to prepare verification history query: " . $mysqli->error);
        }
        
        $stmt->bind_param("i", $documentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        $uploadCount = 0;
        $downloadCount = 0;
        $tamperedRecords = 0;
        
        while ($row = $result->fetch_assoc()) {
            // Verify the integrity of each record
            if (!verifyRecordIntegrity($row)) {
                $tamperedRecords++;
                continue; // Skip tampered records
            }
            
            $history[] = $row;
            if ($row['action'] === 'upload') {
                $uploadCount++;
            } elseif ($row['action'] === 'download') {
                $downloadCount++;
            }
        }
        
        // Check if we have exactly one upload and one download
        $isValid = ($uploadCount === 1 && $downloadCount === 1);
        
        $message = sprintf(
            'Document has %d upload(s) and %d download(s).',
            $uploadCount,
            $downloadCount
        );
        
        if ($tamperedRecords > 0) {
            $message .= sprintf(' Warning: %d record(s) have been tampered with.', $tamperedRecords);
            $isValid = false;
        } else {
            $message .= $isValid ? ' Document integrity is valid.' : ' Document integrity is invalid. Expected exactly 1 upload and 1 download.';
        }
        
        return [
            'success' => true,
            'history' => $history,
            'has_upload' => $uploadCount === 1,
            'has_download' => $downloadCount === 1,
            'upload_count' => $uploadCount,
            'download_count' => $downloadCount,
            'is_valid' => $isValid,
            'status' => $isValid ? 'Valid' : 'Invalid',
            'tampered_records' => $tamperedRecords,
            'message' => $message
        ];
    } catch (Exception $e) {
        error_log("Failed to get document verification history: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Generate a 4-digit verification code for file download
 * @return string 4-digit verification code
 */
function generateVerificationCode() {
    return sprintf('%04d', mt_rand(0, 9999));
} 