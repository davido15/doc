<?php

// Function to generate upload signature
function generateUploadSignature($document_hash, $upload_time, $uploader_email) {
    try {
        $random_salt = bin2hex(random_bytes(16));
        $data = $document_hash . '|' . $upload_time . '|' . $uploader_email . '|' . $random_salt;
        $signature = hash('sha256', $data);
        
        return ['signature' => $signature, 'salt' => $random_salt];
    } catch (Exception $e) {
        error_log('Signature Generation Error: ' . $e->getMessage());
        return null;
    }
}

// Function to generate download signature
function generateDownloadSignature($document_hash, $download_time, $downloader_email, $verification_code, $ip_address = null) {
    try {
        $random_salt = bin2hex(random_bytes(16));
        $data = $document_hash . '|' . $download_time . '|' . $downloader_email . '|' . $verification_code . '|' . ($ip_address ?? '') . '|' . $random_salt;
        $signature = hash('sha256', $data);
        
        return ['signature' => $signature, 'salt' => $random_salt];
    } catch (Exception $e) {
        error_log('Signature Generation Error: ' . $e->getMessage());
        return null;
    }
}

// Function to verify document lifecycle
function verifyDocumentLifecycle($mysqli, $doc_id, $s3Client, $bucketName) {
    // This function will need to be updated to handle the new 'sent' action
    // and correctly verify the signatures with salts.
    // For now, the focus is on logging.
    return null;
}

// Function to get stored verification key from AWS KMS
function getStoredVerificationKey($doc_id) {
    try {
        // Initialize AWS KMS client using the same configuration as S3
        $kms = new KmsClient([
            'region'  => 'eu-north-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => getenv('AWS_ACCESS_KEY_ID') ?: 'test',
                'secret' => getenv('AWS_SECRET_ACCESS_KEY') ?: 'test',
            ],
            'suppress_php_deprecation_warning' => true,
        ]);

        // Get the KMS key ID from environment variable
        $keyId = getenv('AWS_KMS_KEY_ID');
        if (!$keyId) {
            throw new Exception('AWS KMS Key ID not configured');
        }

        // Get the stored key from AWS KMS
        $result = $kms->getPublicKey([
            'KeyId' => $keyId
        ]);

        return $result['PublicKey'];
    } catch (Exception $e) {
        error_log('AWS KMS Error: ' . $e->getMessage());
        return null;
    }
}

// Function to store verification key in AWS KMS
function storeVerificationKey($doc_id, $key) {
    try {
        // Initialize AWS KMS client using the same configuration as S3
        $kms = new KmsClient([
            'region'  => 'eu-north-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => getenv('AWS_ACCESS_KEY_ID') ?: 'test',
                'secret' => getenv('AWS_SECRET_ACCESS_KEY') ?: 'test',
            ],
            'suppress_php_deprecation_warning' => true,
        ]);

        // Get the KMS key ID from environment variable
        $keyId = getenv('AWS_KMS_KEY_ID');
        if (!$keyId) {
            throw new Exception('AWS KMS Key ID not configured');
        }

        // Store the key in AWS KMS
        $result = $kms->importKeyMaterial([
            'KeyId' => $keyId,
            'ImportToken' => $key,
            'EncryptedKeyMaterial' => $key,
            'ExpirationModel' => 'KEY_MATERIAL_DOES_NOT_EXPIRE'
        ]);

        return true;
    } catch (Exception $e) {
        error_log('AWS KMS Error: ' . $e->getMessage());
        return false;
    }
} 