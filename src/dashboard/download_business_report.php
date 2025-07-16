<?php
session_start();
require_once '../functions/db.php';

// Check if user is logged in and has admin access
if (!isset($_SESSION['user_id']) || $_SESSION['organization_id'] != 0) {
    header("Location: logout.php");
    exit();
}

$business_id = $_GET['id'] ?? null;

if (!$business_id) {
    header("Location: /dashboard/business-verifications");
    exit();
}

// Fetch business verification details
$stmt = $mysqli->prepare("SELECT * FROM business_verifications WHERE id = ?");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: /dashboard/business-verifications");
    exit();
}

$business = $result->fetch_assoc();

// Check if report file exists
if (empty($business['report_file'])) {
    header("Location: view_business_verification.php?id=" . $business_id);
    exit();
}

// Since the report is stored as base64 in the database, we need to decode it
$file_content = base64_decode($business['report_file']);

if ($file_content === false) {
    header("Location: view_business_verification.php?id=" . $business_id);
    exit();
}

// Generate a filename based on business name and verification code
$filename = "business_verification_" . $business['verification_code'] . "_" . date('Y-m-d') . ".pdf";

// Set headers for file download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($file_content));
header('Cache-Control: no-cache, must-revalidate');
header('Expires: 0');

// Output file content
echo $file_content;
exit();
?> 