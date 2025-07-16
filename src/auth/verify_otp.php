<?php
session_start();
require_once '../functions/db.php';
require_once '../notifications/OTPHandler.php';

$message = '';
$email = $_SESSION['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resend_otp'])) {
        // Handle resend OTP in the same page
        if (!$email) {
            $message = 'No email found in session.';
        } else {
            require_once '../notifications/NotificationHandler.php';
            $notificationHandler = new NotificationHandler($mysqli);
            if ($notificationHandler->sendOTP('daviddors12@gmail.com')) {
                $message = 'A new verification code has been sent to daviddors12@gmail.com.';
            } else {
                $message = 'Failed to send verification code. Please try again later.';
            }
        }
    } else {
        $otp = trim($_POST['otp'] ?? '');
        if (!$email) {
            $message = 'No email found in session.';
        } elseif (!$otp) {
            $message = 'Please enter the verification code.';
        } else {
            $otpHandler = new OTPHandler($mysqli);
            $result = $otpHandler->verifyOTP('daviddors12@gmail.com', $otp);
            if ($result['success']) {
                // Optionally mark user as verified here
                $update = $mysqli->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
                $update->bind_param("s", $email);
                $update->execute();
                $update->close();
                // Fetch user info and set session variables
                $userStmt = $mysqli->prepare("SELECT u.id, u.email, u.organization_id, o.domain as organization_type FROM users u LEFT JOIN organizations o ON u.organization_id = o.id WHERE u.email = ?");
                if (!$userStmt) {
                    error_log("Prepare failed: " . $mysqli->error);
                    $message = "Internal error. Please contact support.";
                } else {
                    $userStmt->bind_param("s", $email);
                    $userStmt->execute();
                    $userStmt->bind_result($user_id, $user_email, $org_id, $org_type);
                    if ($userStmt->fetch()) {
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['email'] = $user_email;
                        $_SESSION['organization_id'] = $org_id;
                        $_SESSION['organization_type'] = $org_type;
                    }
                    $userStmt->close();
                }
                // Redirect to dashboard based on organization_id
                if (isset($org_id) && $org_id !== null) {
                    switch ($org_id) {
                        case 0:
                            header("Location: ../dashboard");
                            exit();
                        case 1:
                            header("Location: ../bank/dashboard");
                            exit();
                        case 2:
                            header("Location: ../embassy/dashboard");
                            exit();
                    }
                }
                // Default redirect if organization_id is not set or doesn't match
                header("Location: ../dashboard");
                exit();
            } else {
                $message = '❌ ' . $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Verify OTP - DocuPura</title>
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
          <a href="#"><img src="../assets/images/logo-dark.svg" alt="img"></a>
        </div>
        <div class="card my-5">
          <div class="card-body">
            <div class="mb-4">
              <h3 class="mb-2"><b>Enter Verification Code</b></h3>
              <p class="text-muted mb-4">We sent you a code by email.</p>
            </div>
            <?php if ($message): ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form method="post" id="verifyForm">
              <div class="mb-3">
                <input type="text" name="otp" class="form-control" placeholder="Enter verification code" required>
              </div>
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Verify</button>
              </div>
            </form>
            <form method="post" style="margin-top: 10px;" id="resendForm">
              <input type="hidden" name="resend_otp" value="1">
              <button type="submit" class="btn btn-link p-0 m-0 align-baseline">Resend code</button>
            </form>
            <div class="d-flex justify-content-between align-items-end mt-3">
              <p class="mb-0">Did not receive the email? Check your spam filter.</p>
            </div>
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
  </div>
  <!-- [ Main Content ] end -->

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