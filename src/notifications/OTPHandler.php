<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/SMTPMailer.php';

class OTPHandler {
    private $db;
    private $mailer;

    public function __construct($db) {
        $this->db = $db;
        $this->mailer = new SMTPMailer();
    }

    public function generateOTP($email) {
        // Generate a random OTP
        $otp = str_pad(random_int(0, 999999), OTP_LENGTH, '0', STR_PAD_LEFT);
        
        // Store OTP in database using email
        $stmt = $this->db->prepare("INSERT INTO otps (email, code, expires_at, type, is_used) VALUES (?, ?, ?, ?, 0)");
        if (!$stmt) {
            error_log("OTP prepare failed: " . $this->db->error);
            return false;
        }
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $type = 'login';
        $stmt->bind_param("ssss", $email, $otp, $expires_at, $type);
        
        if ($stmt->execute()) {
            return $otp;
        } else {
            error_log("OTP execute failed: " . $stmt->error);
            return false;
        }
    }

    public function sendOTP($email, $otp) {
        // Prepare email content
        $subject = OTP_EMAIL_SUBJECT;
        $body = str_replace('{OTP}', $otp, OTP_EMAIL_TEMPLATE);

        // Send email
        return $this->mailer->sendEmail($email, $subject, $body);
    }

    public function verifyOTP($email, $otp) {
        // Get the latest OTP for the email
        $stmt = $this->db->prepare("
            SELECT code, expires_at, is_used 
            FROM otps 
            WHERE email = ? AND type = 'login'
            ORDER BY id DESC 
            LIMIT 1
        ");
        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error'];
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'No OTP found for this email'];
        }

        $row = $result->fetch_assoc();
        
        // Check if OTP has been used
        if ($row['is_used'] == 1) {
            return ['success' => false, 'message' => 'OTP has already been used'];
        }
        
        // Check if OTP has expired
        if (strtotime($row['expires_at']) < time()) {
            return ['success' => false, 'message' => 'OTP has expired'];
        }

        // Verify OTP
        if ($row['code'] === $otp) {
            // Mark OTP as used
            $updateStmt = $this->db->prepare("UPDATE otps SET is_used = 1 WHERE email = ? AND code = ? AND type = 'login'");
            $updateStmt->bind_param("ss", $email, $otp);
            $updateStmt->execute();
            
            return ['success' => true, 'message' => 'OTP verified successfully'];
        }

        return ['success' => false, 'message' => 'Invalid OTP'];
    }

    public function cleanupExpiredOTPs() {
        // Delete expired OTPs
        $stmt = $this->db->prepare("DELETE FROM otps WHERE expires_at < NOW()");
        $stmt->execute();
    }
} 