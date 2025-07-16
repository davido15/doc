<?php
require_once '../functions/db.php';

$token = $_GET['token'] ?? '';
$message = '';

if ($token) {
    $stmt = $mysqli->prepare("SELECT email, expires_at FROM email_verifications WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($email, $expires_at);
        $stmt->fetch();
        if (strtotime($expires_at) >= time()) {
            // Mark user as verified
            $update = $mysqli->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
            $update->bind_param("s", $email);
            $update->execute();
            $update->close();
            // Delete the token
            $delete = $mysqli->prepare("DELETE FROM email_verifications WHERE token = ?");
            $delete->bind_param("s", $token);
            $delete->execute();
            $delete->close();
            $message = '✅ Your email has been verified. You can now log in.';
        } else {
            $message = '❌ Verification link has expired.';
        }
    } else {
        $message = '❌ Invalid verification link.';
    }
    $stmt->close();
} else {
    $message = '❌ No verification token provided.';
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <title>Email Verification</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h2>Email Verification</h2>
    <p><?php echo htmlspecialchars($message); ?></p>
                                                         <a href="login">Go to Login</a>
</body>
</html> 