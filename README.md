# Technical Documentation

This document provides a technical overview of the key functions used in the document verification system.

## Signature Generation and Verification

These functions are located in `src/functions/signatures.php` and are responsible for creating and verifying the digital signatures that ensure document integrity.

### `generateUploadSignature`

*   **Purpose:** Creates a unique signature for a document when it is first uploaded.
*   **Parameters:**
    *   `$document_hash` (string): The SHA-256 hash of the document's content.
    *   `$upload_time` (int): The Unix timestamp of the upload.
    *   `$uploader_email` (string): The email address of the user uploading the file.
*   **Returns:** A base64-encoded string containing the encrypted signature, or `null` on failure.

### `generateDownloadSignature`

*   **Purpose:** Creates a unique signature for a document when it is downloaded.
*   **Parameters:**
    *   `$document_hash` (string): The SHA-256 hash of the document's content.
    *   `$download_time` (int): The Unix timestamp of the download.
    *   `$downloader_email` (string): The email address of the user downloading the file.
    *   `$verification_code` (string): The verification code used to access the document.
    *   `$ip_address` (string|null): The IP address of the downloader.
*   **Returns:** A base64-encoded string containing the encrypted signature, or `null` on failure.

### `generateLifecycleSignature`

*   **Purpose:** Combines the upload and download signatures into a single, encrypted lifecycle signature.
*   **Parameters:**
    *   `$upload_signature` (string): The signature generated at the time of upload.
    *   `$download_signature` (string): The signature generated at the time of download.
*   **Returns:** A base64-encoded string containing the combined and encrypted signatures, or `null` on failure.

### `verifyDocumentLifecycle`

*   **Purpose:** Performs a comprehensive verification of the document's entire lifecycle, checking for tampering and ensuring the correct sequence of events.
*   **Parameters:**
    *   `$mysqli`: The database connection object.
    *   `$doc_id` (int): The ID of the document to verify.
    *   `$s3Client`: The AWS S3 client object.
    *   `$bucketName` (string): The name of the S3 bucket where the file is stored.
*   **Returns:** An associative array containing the verification status, a list of any issues found, and the document's lifecycle data.

### `generateSecureKey`, `getStoredVerificationKey`, `storeVerificationKey`

*   **Purpose:** These functions are designed for a more advanced key management workflow that is not yet fully implemented. They will be used in the future to generate, retrieve, and store verification keys in AWS KMS, providing an additional layer of security.

## File Handling

These functions are responsible for the upload and download of files.

### `s3upload/upload.php`

*   **Purpose:** Handles the file upload process. It validates the file, encrypts its content, generates a file hash and an upload signature, and then uploads the file to S3. It also records the transaction in the `uploads` and `document_verifications` tables in the database.

### `s3upload/download.php`

*   **Purpose:** Handles the file download process. It verifies the user's access code, retrieves the file from S3, decrypts its content, and logs the download event. It also generates a download signature and updates the S3 object's metadata.

## User Interface and Logic

These files are responsible for rendering the user interface and handling user interactions.

### `src/embassy/view_doc.php`

*   **Purpose:** Displays the document details and verification status to the user. It serves as the main user interface for document verification.

### `src/embassy/view_doc_logic.php`

*   **Purpose:** Contains the business logic for the `view_doc.php` page. It handles user authentication, retrieves document data from the database, and orchestrates the verification process by calling the appropriate signature functions. 