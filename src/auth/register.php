<?php
session_start();
require_once '../functions/db.php';
require_once '../functions/logger.php';
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
$errorMessage = "";
$successMessage = "";
// Generate a CSRF token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // ✅ CSRF token check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      die("CSRF token validation failed.");
    }
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $raw_password = $_POST['password'];
    error_log("Received registration: $first_name $last_name, $email, $phone");
    if (!$first_name || !$last_name || !$email || !$phone || !$raw_password) {
      $errorMessage = "All fields are required.";
      error_log("Validation failed: Missing fields.");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errorMessage = "Invalid email address.";
      error_log("Validation failed: Invalid email.");
    } elseif (strlen($raw_password) < 6) {
      $errorMessage = "Password must be at least 6 characters.";
      error_log("Validation failed: Weak password.");
    } else {
      $password = password_hash($raw_password, PASSWORD_DEFAULT);
      $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
      if (!$stmt) {
        $errorMessage = "Prepare failed: " . $mysqli->error;
        error_log($errorMessage);
      } else {
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
          $errorMessage = "Execute failed: " . $stmt->error;
          error_log($errorMessage);
          logError("Query execution failed: " . $stmt->error);
          throw new Exception("Query execution failed: " . $stmt->error);
        } else {
          $stmt->store_result();
          if ($stmt->num_rows > 0) {
            logError("Email already exists: $email");
            throw new Exception("Email already exists.");
          } else {
            $stmt->close();
            $stmt = $mysqli->prepare("INSERT INTO users (first_name, last_name, phone, email, password) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
              $errorMessage = "Prepare failed: " . $mysqli->error;
              error_log($errorMessage);
            } else {
              $stmt->bind_param("sssss", $first_name, $last_name, $phone, $email, $password);
              if ($stmt->execute()) {
                $successMessage = "Registration successful! Please check your email to verify your account.";
                error_log("User registered: $email");
                // Generate verification token
                $verification_token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 day'));
                $insertTokenStmt = $mysqli->prepare("INSERT INTO email_verifications (email, token, expires_at) VALUES (?, ?, ?)");
                if ($insertTokenStmt) {
                  $insertTokenStmt->bind_param("sss", $email, $verification_token, $expires_at);
                  if (!$insertTokenStmt->execute()) {
                    $errorMessage = "Registration succeeded, but failed to create verification link. Please contact support.";
                    error_log("Failed to insert email verification token: " . $insertTokenStmt->error);
                  }
                  $insertTokenStmt->close();
                } else {
                  $errorMessage = "Registration succeeded, but failed to create verification link. Please contact support.";
                  error_log("Prepare failed for email_verifications: " . $mysqli->error);
                }
                // Send verification email
                require_once '../notifications/NotificationHandler.php';
                $notificationHandler = new NotificationHandler($mysqli);
                $verification_link = "https://" . $_SERVER['HTTP_HOST'] . "/src/auth/verify_email.php?token=" . $verification_token;
                $subject = "Verify Your Email Address";
                $body = "<p>Thank you for registering. Please <a href='$verification_link'>click here to verify your email address</a>. This link will expire in 24 hours.</p>";
                $notificationHandler->sendEmail($email, $subject, $body);
                // ✅ Reset POST and regenerate CSRF token to avoid re-submission
                $_POST = [];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
              } else {
                logError("Error registering user: " . $stmt->error);
                throw new Exception("Error registering user: " . $stmt->error);
              }
            }
          }
        }
      }
      $stmt->close();
    }
  } catch (Exception $e) {
    $errorMessage = $e->getMessage();
    error_log("Registration error: " . $e->getMessage());
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register - DocuPura</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/fonts/feather.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/fonts/fontawesome.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/fonts/material.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style-preset.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/custom.css">
    <style>
    @font-face {
      font-family: tabler-icons;
      font-style: normal;
      font-weight: 400;
      src: url(../fonts/tabler/tabler-icons.eot);
      src: url(../fonts/tabler/tabler-icons.eot?#iefix) format('embedded-opentype'),
           url(../fonts/tabler/tabler-icons.woff2) format('woff2'),
           url(../fonts/tabler/tabler-icons.woff) format('woff'),
           url(../fonts/tabler/tabler-icons.ttf) format('truetype'),
           url(../fonts/tabler/tabler-icons.svg#tabler-icons) format('svg');
    }
    @media screen and (-webkit-min-device-pixel-ratio: 0) {
      @font-face {
        font-family: tabler-icons;
        src: url(../fonts/tabler/tabler-icons.svg#tabler-icons) format('svg');
      }
    }
    </style>
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
                    <?php
          if ($errorMessage) {   
          echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Warning!</strong>'.$errorMessage.
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';}
          if($successMessage){
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Warning!</strong>'.$successMessage.
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
          }
          ?>
                    <form method="post" id="registerForm">
                        <input type="hidden" name="csrf_token"
                            value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-end mb-4">
                                <h3 class="mb-0"><b>Sign up</b></h3>
                                <a href="login" class="link-primary">Already have an account?</a>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">First Name*</label>
                                        <input type="text" name="first_name" class="form-control"
                                            placeholder="First Name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Last Name*</label>
                                        <input type="text" name="last_name" class="form-control" placeholder="Last Name"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Phone Number*</label>
                                <input type="text" name="phone" class="form-control" placeholder="Phone Number"
                                    required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Email Address*</label>
                                <input type="email" name="email" class="form-control" placeholder="Email Address"
                                    required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Password*</label>
                                <input type="password" name="password" class="form-control" placeholder="Password"
                                    required>
                            </div>
                            <p class="mt-4 text-sm text-muted">
                                By signing up, you agree to our
                                <a href="#" class="text-primary">Terms of Service</a> and
                                <a href="#" class="text-primary">Privacy Policy</a>
                            </p>
                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-primary" id="registerBtn">
                                    <span class="btn-text">Create Account</span>
                                    <span class="btn-spinner d-none">
                                        <i class="ti ti-loader-2 ti-spin me-1"></i>Creating Account...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
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
    </div>
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