<?php
require_once '../notifications/SMTPMailer.php';

$mailer = new SMTPMailer();
$to = 'daviddors12@gmail.com'; // Replace with your email address

if ($mailer->sendTestEmail($to)) {
    echo "Test email sent successfully.";
} else {
    echo "Failed to send test email.";
}
?> 