<?php
session_start();
require_once '../functions/db.php';

// Check if user is logged in and has embassy access
if (!isset($_SESSION['user_id']) || $_SESSION['organization_id'] < 2) {
    header("Location: logout.php");
    exit();
}

$business_id = $_GET['id'] ?? null;
$tab = $_GET['tab'] ?? 'details';

if (!$business_id) {
    header("Location: dashboard.php");
    exit();
}

// Fetch business verification details
$stmt = $mysqli->prepare("SELECT * FROM business_verifications WHERE id = ? AND embassy_id = ?");
$stmt->bind_param("ii", $business_id, $_SESSION['organization_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$business = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Business Verification - Embassy</title>
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
                                <li class="breadcrumb-item">View Details</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Business Verification Details</h2>
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
                            <h5><?= htmlspecialchars($business['business_name']) ?></h5>
                            <div class="float-end">
                                <a href="dashboard" class="btn btn-secondary btn-sm">
                                    <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Navigation Tabs -->
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?= $tab === 'details' ? 'active' : '' ?>" 
                                            id="details-tab" data-bs-toggle="tab" data-bs-target="#details" 
                                            type="button" role="tab">Details</button>
                                </li>
                                <?php if ($business['status'] === 'Verified' || $business['status'] === 'Rejected'): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?= $tab === 'report' ? 'active' : '' ?>" 
                                            id="report-tab" data-bs-toggle="tab" data-bs-target="#report" 
                                            type="button" role="tab">Report</button>
                                </li>
                                <?php endif; ?>
                            </ul>
                            
                            <!-- Tab Content -->
                            <div class="tab-content" id="myTabContent">
                                <!-- Details Tab -->
                                <div class="tab-pane fade <?= $tab === 'details' ? 'show active' : '' ?>" 
                                     id="details" role="tabpanel">
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label text-lg-end fw-bold">Business Name:</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-plaintext"><?= htmlspecialchars($business['business_name']) ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label text-lg-end fw-bold">Business Type:</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-plaintext"><?= htmlspecialchars($business['business_type']) ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label text-lg-end fw-bold">Business Address:</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-plaintext"><?= htmlspecialchars($business['business_address']) ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label text-lg-end fw-bold">Contact Person:</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-plaintext"><?= htmlspecialchars($business['contact_person']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label text-lg-end fw-bold">Contact Email:</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-plaintext"><?= htmlspecialchars($business['contact_email']) ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label text-lg-end fw-bold">Contact Phone:</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-plaintext"><?= htmlspecialchars($business['contact_phone']) ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label text-lg-end fw-bold">Status:</label>
                                                <div class="col-lg-8">
                                                    <?php
                                                    $status_class = match($business['status']) {
                                                        'Pending' => 'bg-warning',
                                                        'In Progress' => 'bg-info',
                                                        'Verified' => 'bg-success',
                                                        'Rejected' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                    ?>
                                                    <span class="badge <?= $status_class ?> fs-6"><?= htmlspecialchars($business['status']) ?></span>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label text-lg-end fw-bold">Verification Code:</label>
                                                <div class="col-lg-8">
                                                    <code class="fs-5"><?= $business['verification_code'] ?></code>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label text-lg-end fw-bold">Created:</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-plaintext"><?= date('F d, Y \a\t H:i', strtotime($business['created_at'])) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($business['verification_reason'])): ?>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <label class="col-lg-2 col-form-label text-lg-end fw-bold">Verification Reason:</label>
                                                <div class="col-lg-10">
                                                    <p class="form-control-plaintext"><?= nl2br(htmlspecialchars($business['verification_reason'])) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Report Tab -->
                                <?php if ($business['status'] === 'Verified' || $business['status'] === 'Rejected'): ?>
                                <div class="tab-pane fade <?= $tab === 'report' ? 'show active' : '' ?>" 
                                     id="report" role="tabpanel">
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <?php if (!empty($business['report_file'])): ?>
                                                <div class="alert alert-info">
                                                    <h6><i class="ti ti-file-text me-2"></i>Verification Report Available</h6>
                                                    <p>A verification report has been generated for this business verification request.</p>
                                                    <a href="download_report.php?id=<?= $business['id'] ?>" class="btn btn-primary">
                                                        <i class="ti ti-download me-1"></i> Download Report
                                                    </a>
                                                </div>
                                                
                                                <?php if (!empty($business['notes'])): ?>
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6>Admin Notes</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <p><?= nl2br(htmlspecialchars($business['notes'])) ?></p>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <div class="alert alert-warning">
                                                    <h6><i class="ti ti-alert-triangle me-2"></i>No Report Available</h6>
                                                    <p>No verification report has been generated yet for this business verification request.</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
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
</body>
</html> 