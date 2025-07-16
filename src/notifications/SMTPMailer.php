<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SMTPMailer {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->setupMailer();
    }

    private function setupMailer() {
        try {
            // Server settings
            $this->mailer->SMTPDebug = 0; // Disable debug output for production
            $this->mailer->isSMTP();
            $this->mailer->Host = SMTP_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USERNAME;
            $this->mailer->Password = SMTP_PASSWORD;
            if (SMTP_PORT == 465) {
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $this->mailer->Port = SMTP_PORT;

            // Default sender
            $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            
            // Enable HTML
            $this->mailer->isHTML(true);
        } catch (Exception $e) {
            error_log("SMTP Setup Error: " . $e->getMessage());
        }
    }

    public function sendEmail($to, $subject, $body) {
        try {
            $this->mailer->clearAddresses();
            if (is_array($to)) {
                foreach ($to as $recipient) {
                    $this->mailer->addAddress($recipient);
                }
            } else {
                $this->mailer->addAddress($to);
            }
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email Send Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendEmailWithCC($to, $cc, $subject, $body) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearCCs();
            $this->mailer->addAddress($to);
            $this->mailer->addCC($cc);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email Send Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendTestEmail($to) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->addAddress('daviddors12@gmail.com');
            $this->mailer->Subject = "Test Email";
            $this->mailer->Body = "This is a test email to verify that the email sending functionality is working correctly.";
            $this->mailer->AltBody = "This is a test email to verify that the email sending functionality is working correctly.";

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Test Email Send Error: " . $e->getMessage());
            return false;
        }
    }
} 