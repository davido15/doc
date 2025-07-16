<?php
session_start();
require_once '../functions/db.php';

// Check if user is logged in and has admin access
if (!isset($_SESSION['user_id']) || $_SESSION['organization_id'] != 0) {
    header("Location: logout.php");
    exit();
}

$business_id = $_GET['id'] ?? null;

if (!$business_id) {
    header("Location: /dashboard/business-verifications");
    exit();
}

// Fetch business verification details
$stmt = $mysqli->prepare("SELECT * FROM business_verifications WHERE id = ?");
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: /dashboard/business-verifications");
    exit();
}

$business = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Business Verification - Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style-preset.css">
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include "sidebar.php"; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Business Verification Details</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="business_verifications.php" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5><?= htmlspecialchars($business['business_name']) ?></h5>
                                <div class="float-end">
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
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-4 col-form-label text-lg-end fw-bold">Business Name:</label>
                                            <div class="col-lg-8">
                                                <p class="form-control-plaintext"><?= htmlspecialchars($business['business_name']) ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-4 col-form-label text-lg-end fw-bold">Business Type:</label>
                                            <div class="col-lg-8">
                                                <p class="form-control-plaintext"><?= htmlspecialchars($business['business_type']) ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-4 col-form-label text-lg-end fw-bold">Business Address:</label>
                                            <div class="col-lg-8">
                                                <p class="form-control-plaintext"><?= htmlspecialchars($business['business_address']) ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-4 col-form-label text-lg-end fw-bold">Contact Person:</label>
                                            <div class="col-lg-8">
                                                <p class="form-control-plaintext"><?= htmlspecialchars($business['contact_person']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-4 col-form-label text-lg-end fw-bold">Contact Email:</label>
                                            <div class="col-lg-8">
                                                <p class="form-control-plaintext"><?= htmlspecialchars($business['contact_email']) ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-4 col-form-label text-lg-end fw-bold">Contact Phone:</label>
                                            <div class="col-lg-8">
                                                <p class="form-control-plaintext"><?= htmlspecialchars($business['contact_phone']) ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-4 col-form-label text-lg-end fw-bold">Embassy ID:</label>
                                            <div class="col-lg-8">
                                                <p class="form-control-plaintext"><?= $business['embassy_id'] ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-4 col-form-label text-lg-end fw-bold">Verification Code:</label>
                                            <div class="col-lg-8">
                                                <code class="fs-5"><?= $business['verification_code'] ?></code>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-4 col-form-label text-lg-end fw-bold">Created:</label>
                                            <div class="col-lg-8">
                                                <p class="form-control-plaintext"><?= date('F d, Y \a\t H:i', strtotime($business['created_at'])) ?></p>
                                            </div>
                                        </div>
                                        
                                        <?php if ($business['verified_at']): ?>
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-4 col-form-label text-lg-end fw-bold">Verified At:</label>
                                            <div class="col-lg-8">
                                                <p class="form-control-plaintext"><?= date('F d, Y \a\t H:i', strtotime($business['verified_at'])) ?></p>
                                            </div>
                                        </div>
                                        <?php endif; ?>
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
                                
                                <?php if (!empty($business['notes'])): ?>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label text-lg-end fw-bold">Admin Notes:</label>
                                            <div class="col-lg-10">
                                                <div class="alert alert-info">
                                                    <p class="mb-0"><?= nl2br(htmlspecialchars($business['notes'])) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($business['report_file'])): ?>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label text-lg-end fw-bold">Report File:</label>
                                            <div class="col-lg-10">
                                                <div class="alert alert-success">
                                                    <h6><i class="ti ti-file-text me-2"></i>Verification Report Available</h6>
                                                    <p>A verification report has been uploaded for this business verification request.</p>
                                                    <a href="download_business_report.php?id=<?= $business['id'] ?>" class="btn btn-primary">
                                                        <i class="ti ti-download me-1"></i> Download Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="row mt-4">
                                    <div class="col-12 text-center">
                                        <?php if (in_array($business['status'], ['Pending', 'In Progress'])): ?>
                                        <button class="btn btn-success" onclick="uploadReport(<?= $business['id'] ?>)">
                                            <i class="ti ti-upload me-1"></i> Upload Report
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Upload Report Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Verification Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" action="business_verifications.php">
                    <div class="modal-body">
                        <input type="hidden" name="verification_id" id="uploadVerificationId">
                        <input type="hidden" name="upload_report" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select" name="verification_status" required>
                                <option value="">Select status</option>
                                <option value="Verified">Verified</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Report File</label>
                            <input type="file" class="form-control" name="report_file" accept=".pdf,.doc,.docx">
                            <small class="form-text text-muted">PDF or Word documents only</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function uploadReport(id) {
        $("#uploadVerificationId").val(id);
        $("#uploadModal").modal("show");
    }
    </script>
</body>
</html> 