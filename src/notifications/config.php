<?php
// SMTP Configuration
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'email-smtp.us-east-1.amazonaws.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 465);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: 'AKIAX4NX6UPYZCAAAR7D');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: 'BB1QutKFGeNkk9l1v6nfxyi879gpCx8Abe349zSWk9Z1');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'info@docupura.com');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'DocuPura');



// OTP Configuration
define('OTP_EXPIRY_MINUTES', 10);
define('OTP_MAX_ATTEMPTS', 3);
define('OTP_LENGTH', 6);

// Email Templates
define('EMAIL_HEADER_COLOR', '#333333');
define('EMAIL_BUTTON_COLOR', '#007bff');
define('EMAIL_FOOTER_COLOR', '#666666');

// Security
define('PASSWORD_RESET_EXPIRY_HOURS', 1);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_MINUTES', 15);

// Error Messages
define('ERROR_INVALID_OTP', 'Invalid or expired verification code.');
define('ERROR_MAX_ATTEMPTS', 'Maximum attempts reached. Please try again later.');
define('ERROR_EMAIL_SEND', 'Failed to send email. Please try again later.');
define('ERROR_INVALID_TOKEN', 'Invalid or expired token.');

// Email Templates
define('OTP_EMAIL_SUBJECT', 'Your Verification Code - PDF Verifier');
define('OTP_EMAIL_TEMPLATE', '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #333;">Verification Code</h2>
        <p>Your one-time password (OTP) is: <strong style="font-size: 24px; color: #007bff;">{OTP}</strong></p>
        <p>This code will expire in 10 minutes.</p>
        <p>If you did not request this code, please ignore this email.</p>
        <hr>
        <p style="color: #666; font-size: 12px;">This is an automated message, please do not reply.</p>
    </div>
');

define('DOC_ACCESS_EMAIL_SUBJECT', 'Document Access Code');
define('DOC_ACCESS_EMAIL_TEMPLATE', '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #333;">Document Access Code</h2>
        <p>Your document access code is: <strong style="font-size: 24px; color: #007bff;">{CODE}</strong></p>
        <p>This code will expire in 6 months.</p>
        <p>If you did not request this code, please ignore this email.</p>
        <hr>
        <p style="color: #666; font-size: 12px;">This is an automated message, please do not reply.</p>
    </div>
'); 