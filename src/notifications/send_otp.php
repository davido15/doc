<?php
include 'smtp_mailer.php'; // adjust path as needed
include 'send_sms';
// Example usage
$to = "daviddors12@gmail.com";
$subject = "Verify your email";
$body = file_get_contents("header.html");
$body .= '<p style="text-align:center">Your code: <strong>123456</strong></p>';
$body .= file_get_contents("footer.html");
$sent = send_smtp_email($to, $subject, $body, "ddornyoh@outrankconsult.com", "Your App Name");
if ($sent) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}
?>
