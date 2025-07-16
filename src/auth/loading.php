<?php
session_start();

// Get the target action from session
$action = $_SESSION['loading_action'] ?? 'login';
$target_page = $_SESSION['loading_target'] ?? 'login.php';

// Clear the session variables
unset($_SESSION['loading_action']);
unset($_SESSION['loading_target']);

// Redirect after a short delay
header("Refresh: 0.1; URL=$target_page");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Loading - DocuPura</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.svg" type="image/x-icon">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            font-family: 'Public Sans', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .loading-container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-text {
            color: #333;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .loading-subtext {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="spinner"></div>
        <div class="loading-text">
            <?php
            switch($action) {
                case 'login':
                    echo 'Logging in...';
                    break;
                case 'register':
                    echo 'Creating account...';
                    break;
                case 'verify_otp':
                    echo 'Verifying code...';
                    break;
                case 'resend_otp':
                    echo 'Sending code...';
                    break;
                case 'forgot_password':
                    echo 'Sending reset link...';
                    break;
                default:
                    echo 'Processing...';
            }
            ?>
        </div>
        <div class="loading-subtext">Please wait...</div>
    </div>
</body>
</html> 