<?php
require_once 'config.php';

class LoginAttemptHandler {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function recordFailedAttempt($email) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO login_attempts (email, attempt_time)
                VALUES (?, NOW())
            ");
            return $stmt->execute([$email]);
        } catch (Exception $e) {
            error_log("Error recording failed login attempt: " . $e->getMessage());
            return false;
        }
    }
    
    public function isAccountLocked($email) {
        try {
            $lockoutTime = date('Y-m-d H:i:s', strtotime('-' . LOGIN_LOCKOUT_MINUTES . ' minutes'));
            
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as attempt_count
                FROM login_attempts
                WHERE email = ? AND attempt_time > ?
            ");
            $stmt->execute([$email, $lockoutTime]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['attempt_count'] >= MAX_LOGIN_ATTEMPTS;
        } catch (Exception $e) {
            error_log("Error checking account lock status: " . $e->getMessage());
            return true; // Default to locked in case of error
        }
    }
    
    public function getRemainingLockoutTime($email) {
        try {
            $stmt = $this->db->prepare("
                SELECT MAX(attempt_time) as last_attempt
                FROM login_attempts
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result['last_attempt']) {
                return 0;
            }
            
            $lockoutEnd = strtotime($result['last_attempt'] . ' + ' . LOGIN_LOCKOUT_MINUTES . ' minutes');
            $remaining = $lockoutEnd - time();
            
            return max(0, $remaining);
        } catch (Exception $e) {
            error_log("Error getting remaining lockout time: " . $e->getMessage());
            return 0;
        }
    }
    
    public function clearFailedAttempts($email) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM login_attempts
                WHERE email = ?
            ");
            return $stmt->execute([$email]);
        } catch (Exception $e) {
            error_log("Error clearing failed attempts: " . $e->getMessage());
            return false;
        }
    }
    
    public function cleanupOldAttempts() {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM login_attempts
                WHERE attempt_time < DATE_SUB(NOW(), INTERVAL ? MINUTE)
            ");
            return $stmt->execute([LOGIN_LOCKOUT_MINUTES]);
        } catch (Exception $e) {
            error_log("Error cleaning up old login attempts: " . $e->getMessage());
            return false;
        }
    }
} 