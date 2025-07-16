<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['organization_type'] !== 'Embassy') {
    header("Location: /login");
    exit();
}

require_once '../functions/db.php';

// Get document ID from URL
$doc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get document details
$stmt = $mysqli->prepare("SELECT * FROM uploads WHERE id = ? AND embassy_id = ?");
$stmt->bind_param("ii", $doc_id, $_SESSION['organization_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard");
    exit();
}

$document = $result->fetch_assoc();

// Handle verification code submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify_code'])) {
        $input_code = $_POST['verification_code'];
        if ($input_code == $document['verification_code']) {
            $_SESSION['verified_doc_' . $doc_id] = true;
            header("Location: view_doc?id=" . $doc_id);
            exit();
        } else {
            $error = "Invalid verification code";
        }
    } elseif (isset($_POST['verify'])) {
        $stmt = $mysqli->prepare("UPDATE uploads SET Status = 'Verified' WHERE id = ? AND embassy_id = ?");
        $stmt->bind_param("ii", $doc_id, $_SESSION['organization_id']);
        if ($stmt->execute()) {
            header("Location: view_doc?id=" . $doc_id);
            exit();
        }
    }
}

// Check if document is verified for viewing
$is_verified = isset($_SESSION['verified_doc_' . $doc_id]) && $_SESSION['verified_doc_' . $doc_id]; 