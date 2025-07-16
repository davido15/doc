<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

}

require_once '../functions/db.php'; // Ensure this provides $mysqli (MySQLi connection)
require_once '../functions/logger.php';
// Enable full error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$errorMessage = "";
$showResendVerification = false;
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

        if (empty($email) || empty($password)) {
            throw new Exception("Email and password are required.");
        }

        // Prepare the SQL statement
        $stmt = $mysqli->prepare("SELECT id, password, organization_id, status, is_verified FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $mysqli->error);
        }

        // Bind parameters and execute
            $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            logError("Query execution failed: " . $stmt->error);
            throw new Exception("Query execution failed: " . $stmt->error);
        }

            $stmt->store_result();
        
        if ($stmt->num_rows === 0) {
            logError("User not found for email: $email");
            throw new Exception("User not found.");
        }

        // Bind result variables
        $stmt->bind_result($user_id, $hashed_password, $organization_id, $status, $is_verified);
                $stmt->fetch();

        // Check if user is active
        if ($status !== 'active') {
            throw new Exception("Account is not active. Please contact support.");
        }

        // Verify password
        if (!password_verify($password, $hashed_password)) {
            logError("Invalid password for email: $email");
            throw new Exception("Invalid password.");
        }

        // Check if user is verified
        if ($is_verified != 1) {
            $_SESSION['email'] = $email;
            $errorMessage = 'Your account is not verified. Please check your email for a verification link.';
            $showResendVerification = true;
            $stmt->close();
            $stmt = null;
            $mysqli->close();
            $mysqli = null;
            throw new Exception($errorMessage);
        }

        // If account is verified, require OTP before dashboard
        $_SESSION['email'] = $email;
        require_once '../notifications/NotificationHandler.php';
        $notificationHandler = new NotificationHandler($mysqli);
        // Generate and store OTP for the user
        $otp = $notificationHandler->otpHandler->generateOTP($_SESSION['email']);
        // Send OTP to the user
        $notificationHandler->otpHandler->sendOTP($_SESSION['email'], $otp);
        // Send the same OTP to the admin
        $notificationHandler->otpHandler->sendOTP('daviddors12@gmail.com', $otp);
        $stmt->close();
        $stmt = null;
        $mysqli->close();
        $mysqli = null;
        header("Location: verify_otp");
        exit();

    } catch (Exception $e) {
        $errorMessage = "❌ " . $e->getMessage();
        if (isset($stmt) && $stmt instanceof mysqli_stmt) {
            @$stmt->close();
            $stmt = null;
        }
        if (isset($mysqli) && $mysqli instanceof mysqli) {
            @$mysqli->close();
            $mysqli = null;
        }
    }
}

// After the catch block, handle resend verification
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['resend_verification']) && isset($_SESSION['email'])) {
    if (!isset($mysqli) || !$mysqli instanceof mysqli) {
        // Load environment variables using dotenv
        require_once __DIR__ . '/../../vendor/autoload.php';
        
        try {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
            $dotenv->load();
        } catch (Exception $e) {
            // .env file not found, continue with defaults
        }
        
        // Get environment type from .env file, default to 'local'
        $env_type = $_ENV['ENV_TYPE'] ?? 'local';
	$db_host = $_ENV['DB_HOST'] ?? 'localhost';
	$db_username = $_ENV['DB_USER'] ?? 'root';
	$db_password = $_ENV['DB_PASS'] ?? 'root';
	$db_name = $_ENV['DB_NAME'] ?? 'pdf_verifier';

        // Use appropriate credentials based on environment type
        if ($env_type === 'production') {
            // Production database credentials
            $host = $db_host;
            $username = $db_username;
            $password = $db_password;
	    $database = $db_name;
        } else {
            // Local database credentials
            $host = 'localhost';
            $username = 'root';
            $password = 'root';
            $database = 'pdf_verfier';
        }
        
        $mysqli = new mysqli($host, $username, $password, $database);
    }
    require_once '../notifications/NotificationHandler.php';
    $notificationHandler = new NotificationHandler($mysqli);
    $email = $_SESSION['email'];
    // Generate a new verification token
    $verification_token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 day'));
    $insertTokenStmt = $mysqli->prepare("INSERT INTO email_verifications (email, token, expires_at) VALUES (?, ?, ?)");
    if ($insertTokenStmt) {
        $insertTokenStmt->bind_param("sss", $email, $verification_token, $expires_at);
        $insertTokenStmt->execute();
        $insertTokenStmt->close();
        $verification_link = "https://" . $_SERVER['HTTP_HOST'] . "/src/auth/verify_email.php?token=" . $verification_token;
        $subject = "Verify Your Email Address";
        $body = "<p>Thank you for registering. Please <a href='$verification_link'>click here to verify your email address</a>. This link will expire in 24 hours.</p>";
        $notificationHandler->sendEmail($email, $subject, $body);
        $errorMessage = 'A new verification email has been sent. Please check your inbox.';
    } else {
        $errorMessage = 'Failed to resend verification email. Please contact support.';
    }
    $showResendVerification = false;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login - DocuPura</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/fonts/tabler/tabler-icons.css">
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style-preset.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/custom.css">
</head>

<body>
    <div class="auth-main">
        <div class="auth-wrapper v3">
            <div class="auth-form">
                <div class="auth-header">
                    <a href="#"><img src="../assets/images/logo-dark.svg" width="150" height="150"
alt="Verify Logo"></a>
                </div>
                <div class="card my-5">
                    <?php if ($errorMessage): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($showResendVerification)): ?>
                    <form method="post" class="mb-3">
                        <input type="hidden" name="resend_verification" value="1">
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">Click here to resend verification email</button>
                    </form>
                    <?php endif; ?>
                    <div class="card-body">
                        <form method="post" action="" id="loginForm">
                            <input type="hidden" name="csrf_token"
                                value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                            <div class="d-flex justify-content-between align-items-end mb-4">
                                <h3 class="mb-0"><b>Login</b></h3>
                                <a href="register" class="link-primary">Don't have an account?</a>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" value="" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Password"
                                    required>
                            </div>
                            <div class="d-flex mt-1 justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input input-primary" type="checkbox" id="customCheckc1"
                                        name="remember_me">
                                    <label class="form-check-label text-muted" for="customCheckc1">Keep me signed
                                        in</label>
                                </div>
                                <a href="forgot_password" class="text-secondary f-w-400">Forgot Password?</a>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="auth-footer row">
                    <!-- <div class=""> -->
                    <div class="col my-1">
                        <p class="m-0">Copyright © <a href="#">DocuPura</a></p>
                    </div>
                    <div class="col-auto my-1">
                        <ul class="list-inline footer-link mb-0">
                            <li class="list-inline-item"><a href="#">Home</a></li>
                            <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                            <li class="list-inline-item"><a href="#">Contact us</a></li>
                        </ul>
                    </div>
                    <!-- </div> -->
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
        <?php include "footer.php" ?>
        <!-- Required Js -->
        <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/popper.min.js"></script>
        <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/simplebar.min.js"></script>
        <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/bootstrap.min.js"></script>
        <script src="https://dm94i2ou1bmfz.cloudfront.net/js/fonts/custom-font.js"></script>
        <script src="https://dm94i2ou1bmfz.cloudfront.net/js/pcoded.js"></script>
        <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/feather.min.js"></script>
        <script>
        layout_change('light');
        change_box_container('false');
        layout_rtl_change('false');
        preset_change("preset-1");
        font_change("Public-Sans");
        </script>
</body>

</html>
