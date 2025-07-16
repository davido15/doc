<?php
session_start();
require_once '../functions/db.php';

// Check if user is logged in and has embassy access
if (!isset($_SESSION['user_id']) || $_SESSION['organization_id'] < 2) {
    header("Location: logout.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $business_name = trim($_POST['business_name'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');
    $verification_reason = trim($_POST['verification_reason'] ?? '');
    $embassy_id = $_SESSION['organization_id'];
    
    // Validation
    if (empty($business_name)) {
        $error_message = "Business name is required.";
    } elseif (empty($contact_email) || !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Valid contact email is required.";
    } elseif (empty($contact_phone)) {
        $error_message = "Contact phone is required.";
    } else {
        // Generate unique verification code
        do {
            $verification_code = random_int(10000000, 99999999);
            $stmt = $mysqli->prepare("SELECT id FROM business_verifications WHERE verification_code = ?");
            $stmt->bind_param("i", $verification_code);
            $stmt->execute();
            $stmt->store_result();
        } while ($stmt->num_rows > 0);
        
        // Insert business verification request
        $stmt = $mysqli->prepare("INSERT INTO business_verifications (
            embassy_id, business_name, business_type, business_address, 
            contact_person, contact_email, contact_phone, verification_reason, 
            verification_code, status, created_at
        ) VALUES (?, ?, 'NA', 'NA', 'NA', ?, ?, ?, ?, 'Pending', NOW())");
        
        if ($stmt) {
            $stmt->bind_param("isssss", $embassy_id, $business_name, $contact_email, 
                             $contact_phone, $verification_reason, $verification_code);
            
            if ($stmt->execute()) {
                $success_message = "Business verification request submitted successfully! Verification Code: " . $verification_code;
                // Clear form data after successful submission
                $_POST = array();
            } else {
                $error_message = "Error submitting request: " . $stmt->error;
            }
        } else {
            $error_message = "Database error: " . $mysqli->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Business Verification - Embassy</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style-preset.css">
</head>
<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- Pre-loader -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    
    <?php include 'sidebar.php'; ?>
    
    <!-- Main Content -->
    <section class="pc-container">
        <div class="pc-content">
            <!-- Breadcrumb -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                                <li class="breadcrumb-item">Business Verification</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Business Verification Request</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Submit Business for Verification</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($success_message): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($success_message) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($error_message) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label text-lg-end">Business Name *</label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="business_name" 
                                               value="<?= htmlspecialchars($_POST['business_name'] ?? '') ?>" required>
                                        <small class="form-text text-muted">Enter the name of the business to be verified</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label text-lg-end">Contact Email *</label>
                                    <div class="col-lg-6">
                                        <input type="email" class="form-control" name="contact_email" 
                                               value="<?= htmlspecialchars($_POST['contact_email'] ?? '') ?>" required>
                                        <small class="form-text text-muted">Email address for contact purposes</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label text-lg-end">Contact Phone *</label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="contact_phone" 
                                               value="<?= htmlspecialchars($_POST['contact_phone'] ?? '') ?>" 
                                               placeholder="+1-234-567-8900" required>
                                        <small class="form-text text-muted">Phone number for contact purposes</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label text-lg-end">Verification Reason</label>
                                    <div class="col-lg-6">
                                        <textarea class="form-control" name="verification_reason" rows="3" 
                                                  placeholder="Explain why this business needs verification"><?= htmlspecialchars($_POST['verification_reason'] ?? '') ?></textarea>
                                        <small class="form-text text-muted">Optional: Explain the reason for verification</small>
                                    </div>
                                </div>
                                
                                <div class="text-center m-t-20">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                    <a href="dashboard" class="btn btn-secondary ms-2">
                                        <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col-sm my-1">
                    <p class="m-0">DocuPura &#9829; <a href="https://themeforest.net/user/codedthemes" target="_blank">Copyright</a></p>
                </div>
                <div class="col-auto my-1">
                    <ul class="list-inline footer-link mb-0">
                        <li class="list-inline-item"><a href="dashboard">Home</a></li>
                        <li class="list-inline-item"><a href="" target="_blank">Documentation</a></li>
                        <li class="list-inline-item"><a href="#" target="_blank">Support</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/popper.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/simplebar.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/bootstrap.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/fonts/custom-font.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/pcoded.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/feather.min.js"></script>
    
    <script>
    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
    </script>
</body>
</html> 