<?php
session_start();
require_once '../functions/db.php';
require_once '../notifications/PasswordResetHandler.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';
$email = '';

if (empty($token)) {
    header('Location: /login');
    exit;
}

$resetHandler = new PasswordResetHandler($mysqli);
$email = $resetHandler->verifyResetToken($token);

if (!$email) {
    $error = 'Invalid or expired reset link. Please request a new password reset.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirm_password)) {
        $error = 'Please enter and confirm your new password.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        if ($resetHandler->resetPassword($token, $password)) {
            $success = 'Your password has been reset successfully. You can now login with your new password.';
        } else {
            $error = 'Failed to reset password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password - DocuPura</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/fonts/tabler/tabler-icons.css">
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style-preset.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/custom.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Reset Password</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                                <div class="mt-3">
                                    <a href="login" class="btn btn-primary">Go to Login</a>
                                </div>
                            </div>
                        <?php elseif ($email): ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required 
                                           minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                                           title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
                                    <div class="form-text">Password must be at least 8 characters long and include uppercase, lowercase, and numbers.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Reset Password</button>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                        <?php if (!$success && !$email): ?>
                            <div class="text-center mt-3">
                                <a href="forgot_password" class="btn btn-primary">Request New Reset Link</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

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