# Veritana Notification System

This directory contains the notification system components for the Veritana application. The system handles email notifications, OTP verification, password resets, and login security.

## Components

### Core Classes

1. `NotificationHandler.php`
   - Main class that combines OTP and SMTP functionality
   - Handles sending various types of emails (OTP, password reset, welcome)
   - Uses HTML templates for email formatting

2. `OTPHandler.php`
   - Manages OTP generation and verification
   - Handles OTP expiry and attempt tracking
   - Stores OTPs in the database

3. `SMTPMailer.php`
   - Handles email sending using PHPMailer
   - Configures SMTP settings
   - Provides error handling for email operations

4. `PasswordResetHandler.php`
   - Manages password reset functionality
   - Generates and verifies reset tokens
   - Handles password updates

5. `LoginAttemptHandler.php`
   - Tracks failed login attempts
   - Implements account lockout functionality
   - Manages login security

### Configuration

- `config.php`
  - SMTP settings
  - OTP configuration
  - Security parameters
  - Error messages

### Database Tables

1. `otps.sql`
   - Stores OTP records
   - Tracks attempts and expiry

2. `password_resets.sql`
   - Stores password reset tokens
   - Manages token expiry and usage

3. `login_attempts.sql`
   - Tracks failed login attempts
   - Implements account lockout

## Setup

1. Install required dependencies:
   ```bash
   composer require phpmailer/phpmailer
   ```

2. Configure SMTP settings in `config.php`:
   ```php
   define('SMTP_HOST', 'your-smtp-host');
   define('SMTP_PORT', 587);
   define('SMTP_USERNAME', 'your-email@example.com');
   define('SMTP_PASSWORD', 'your-password');
   ```

3. Create database tables:
   ```sql
   source otps.sql
   source password_resets.sql
   source login_attempts.sql
   ```

## Usage

### Sending OTP
```php
$notificationHandler = new NotificationHandler();
$notificationHandler->sendOTP('user@example.com');
```

### Verifying OTP
```php
$notificationHandler = new NotificationHandler();
$isValid = $notificationHandler->verifyOTP('user@example.com', '123456');
```

### Password Reset
```php
$resetHandler = new PasswordResetHandler($db);
$resetHandler->generateResetToken('user@example.com');
```

### Login Security
```php
$loginHandler = new LoginAttemptHandler($db);
if ($loginHandler->isAccountLocked('user@example.com')) {
    // Handle locked account
}
```

## Security Features

1. OTP Security
   - 6-digit numeric codes
   - 10-minute expiry
   - Maximum 3 attempts

2. Password Reset
   - Secure token generation
   - 1-hour expiry
   - One-time use tokens

3. Login Protection
   - Account lockout after 5 failed attempts
   - 15-minute lockout period
   - IP address tracking

## Error Handling

All components include comprehensive error handling:
- Database errors are logged
- SMTP errors are caught and logged
- Invalid attempts are tracked
- Security breaches are prevented

## Maintenance

Regular maintenance tasks:
1. Clean up expired OTPs
2. Remove used password reset tokens
3. Clear old login attempts
4. Monitor error logs

## Contributing

When contributing to this system:
1. Follow the existing code style
2. Add appropriate error handling
3. Update documentation
4. Test thoroughly
5. Consider security implications 