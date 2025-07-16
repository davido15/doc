<?php
session_start();
require_once "../functions/db.php";

// Check admin access
if (!isset($_SESSION["user_id"]) || $_SESSION["organization_id"] != 0) {
    header("Location: /logout");
    exit();
}

$success_message = "";
$error_message = "";

// Handle report upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["upload_report"])) {
    $verification_id = intval($_POST["verification_id"]);
    $verification_status = $_POST["verification_status"];
    $notes = trim($_POST["notes"] ?? "");
    
    // Handle file upload
    $report_file = null;
    if (isset($_FILES["report_file"]) && $_FILES["report_file"]["error"] === 0) {
        $allowed_types = ["application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
        $file_type = $_FILES["report_file"]["type"];
        
        if (in_array($file_type, $allowed_types)) {
            $file_content = file_get_contents($_FILES["report_file"]["tmp_name"]);
            $report_file = base64_encode($file_content);
        } else {
            $error_message = "Invalid file type. Only PDF and Word documents are allowed.";
        }
    }
    
    if (empty($error_message)) {
        $stmt = $mysqli->prepare("UPDATE business_verifications SET status = ?, notes = ?, verified_by = ?, verified_at = NOW(), report_file = ? WHERE id = ?");
        
        if ($stmt) {
            $stmt->bind_param("ssisi", $verification_status, $notes, $_SESSION["user_id"], $report_file, $verification_id);
            if ($stmt->execute()) {
                $success_message = "Verification report uploaded successfully!";
            } else {
                $error_message = "Error updating verification: " . $stmt->error;
            }
        }
    }
}

// Fetch all business verification requests
$stmt = $mysqli->prepare("SELECT bv.* FROM business_verifications bv ORDER BY bv.created_at DESC");
if ($stmt) {
    $stmt->execute();
    $verifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $error_message = "Database error: " . $mysqli->error;
    $verifications = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Business Verifications - Admin</title>
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
                    <h1 class="h2">Business Verifications</h1>
                </div>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="verifications-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Business Name</th>
                                        <th>Embassy</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Code</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($verifications as $v): ?>
                                    <tr>
                                        <td><?= $v["id"] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($v["business_name"]) ?></strong>
                                            <?php if ($v["business_type"]): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($v["business_type"]) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars("Embassy ID: " . $v["embassy_id"]) ?></td>
                                        <td>
                                            <?= htmlspecialchars($v["contact_person"] ?: $v["contact_email"]) ?>
                                            <br><small><?= htmlspecialchars($v["contact_phone"]) ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = match($v["status"]) {
                                                "Pending" => "bg-warning",
                                                "In Progress" => "bg-info",
                                                "Verified" => "bg-success",
                                                "Rejected" => "bg-danger",
                                                default => "bg-secondary"
                                            };
                                            ?>
                                            <span class="badge <?= $status_class ?>"><?= htmlspecialchars($v["status"]) ?></span>
                                        </td>
                                        <td><code><?= $v["verification_code"] ?></code></td>
                                        <td><?= date("M d, Y H:i", strtotime($v["created_at"])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="viewDetails(<?= $v["id"] ?>)">
                                                <i class="ti ti-eye"></i> View
                                            </button>
                                            <?php if (in_array($v["status"], ["Pending", "In Progress"])): ?>
                                            <button class="btn btn-sm btn-success" onclick="uploadReport(<?= $v["id"] ?>)">
                                                <i class="ti ti-upload"></i> Upload Report
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
                <form method="POST" enctype="multipart/form-data">
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
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <script>
    $(document).ready(function() {
        $("#verifications-table").DataTable({
            responsive: true,
            order: [[6, "desc"]]
        });
    });
    
    function uploadReport(id) {
        $("#uploadVerificationId").val(id);
        $("#uploadModal").modal("show");
    }
    
    function viewDetails(id) {
        window.location.href = 'view_business_verification.php?id=' + id;
    }
    </script>
</body>
</html>
