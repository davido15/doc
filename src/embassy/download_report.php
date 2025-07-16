<?php
session_start();
require_once '../functions/db.php';

// Check if user is logged in and has embassy access
if (!isset($_SESSION['user_id']) || $_SESSION['organization_id'] < 2) {
    header("Location: logout.php");
    exit();
}

$business_id = $_GET['id'] ?? null;

if (!$business_id) {
    header("Location: dashboard.php");
    exit();
}

// Fetch business verification details
$stmt = $mysqli->prepare("SELECT * FROM business_verifications WHERE id = ? AND embassy_id = ?");
$stmt->bind_param("ii", $business_id, $_SESSION['organization_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$business = $result->fetch_assoc();

// Check if report file exists
if (empty($business['report_file'])) {
    header("Location: view_business_verification.php?id=" . $business_id . "&tab=report");
    exit();
}

$file_path = '../uploads/reports/' . $business['report_file'];

if (!file_exists($file_path)) {
    header("Location: view_business_verification.php?id=" . $business_id . "&tab=report");
    exit();
}

// Set headers for file download
$file_extension = pathinfo($business['report_file'], PATHINFO_EXTENSION);
$content_type = match($file_extension) {
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    default => 'application/octet-stream'
};

header('Content-Type: ' . $content_type);
header('Content-Disposition: attachment; filename="' . $business['report_file'] . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: no-cache, must-revalidate');
header('Expires: 0');

// Output file content
readfile($file_path);
exit();
?> 