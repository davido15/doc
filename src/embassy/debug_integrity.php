<?php
session_start();

// Check if user is logged in and has embassy access
if (!isset($_SESSION['user_id']) || $_SESSION['organization_type'] !== 'Embassy') {
    header("Location: ../login.php");
    exit();
}

require_once '../functions/db.php';
require_once '../functions/config.php';

$document_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

echo "<h2>Debug: Integrity Verification Logs</h2>";
echo "<p>Document ID: $document_id</p>";

// Check all document_verifications for this document
$stmt = $mysqli->prepare("SELECT * FROM document_verifications WHERE document_id = ? ORDER BY timestamp DESC");
$stmt->bind_param("i", $document_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h3>All Document Verifications:</h3>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Document ID</th><th>Email</th><th>Action</th><th>Timestamp</th><th>Digital Signature</th><th>Salt</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['document_id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['action']) . "</td>";
    echo "<td>" . date('Y-m-d H:i:s', $row['timestamp']) . "</td>";
    echo "<td>" . htmlspecialchars($row['digital_signature']) . "</td>";
    echo "<td>" . htmlspecialchars(substr($row['salt'], 0, 20)) . "...</td>";
    echo "</tr>";
}

echo "</table>";

// Check specifically for integrity verifications
$stmt = $mysqli->prepare("SELECT * FROM document_verifications WHERE document_id = ? AND action IN ('integrity_pass', 'integrity_fail') ORDER BY timestamp DESC");
$stmt->bind_param("i", $document_id);
$stmt->execute();
$integrity_result = $stmt->get_result();

echo "<h3>Integrity Verifications Only:</h3>";
echo "<p>Found " . $integrity_result->num_rows . " integrity verification records</p>";

if ($integrity_result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Document ID</th><th>Email</th><th>Action</th><th>Timestamp</th><th>Digital Signature</th><th>Salt</th></tr>";
    
    while ($row = $integrity_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['document_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['action']) . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $row['timestamp']) . "</td>";
        echo "<td>" . htmlspecialchars($row['digital_signature']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($row['salt'], 0, 20)) . "...</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No integrity verification records found.</p>";
}

echo "<hr>";
echo "<p><a href='view_doc.php?id=$document_id'>Back to Document View</a></p>";
?> 