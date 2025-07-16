<?php
session_start();
require_once '../functions/db.php';
require_once '../notifications/PasswordResetHandler.php';
require_once '../notifications/NotificationHandler.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        $resetHandler = new PasswordResetHandler($mysqli);
        
        if ($resetHandler->generateResetToken($email)) {
            $success = 'Password reset instructions have been sent to your email.';
        } else {
            $error = 'If an account exists with this email, you will receive password reset instructions.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password - DocuPura</title>
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
                        <h2 class="text-center mb-4">Forgot Password</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="resetForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="form-text">Enter your email address and we'll send you instructions to reset your password.</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="resetBtn">
                                    <span class="btn-text">Send Reset Link</span>
                                    <span class="btn-spinner d-none">
                                        <i class="ti ti-loader-2 ti-spin me-1"></i>Sending...
                                    </span>
                                </button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="login">Back to Login</a>
                        </div>
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
        
        // Loading functionality for forgot password form
        document.getElementById('resetForm').addEventListener('submit', function() {
            const btn = document.getElementById('resetBtn');
            const btnText = btn.querySelector('.btn-text');
            const btnSpinner = btn.querySelector('.btn-spinner');
            const form = this;
            
            // Show loading state
            btn.disabled = true;
            btnText.classList.add('d-none');
            btnSpinner.classList.remove('d-none');
            
            // Disable all form inputs
            const inputs = form.querySelectorAll('input, button');
            inputs.forEach(input => input.disabled = true);
        });
    </script>
</body>
</html> 