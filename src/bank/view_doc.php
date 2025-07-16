<?php
session_start();
require_once '../functions/db.php';  // Updated path
require_once '../functions/signatures.php';

// Get ID from URL or POST
$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
if (!$id) {
    $error_message = "❌ Invalid or missing document ID.";
}

// Handle embassy selection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['embassy_id']) || empty($_POST['embassy_id'])) {
        $error_message = "Please select an embassy.";
    } else {
        $embassyId = $_POST['embassy_id'];
        
        // Update the status, and set both embassy_id and organization_id to the embassy's ID
        $updateStmt = $mysqli->prepare("UPDATE uploads SET Status = 'With Embassy', embassy_id = ?, organization_id = ? WHERE id = ?");
        if (!$updateStmt) {
            $error_message = "Prepare failed: " . $mysqli->error;
        } else {
            $updateStmt->bind_param("iii", $embassyId, $embassyId, $id);
            
            if ($updateStmt->execute()) {
                if ($updateStmt->affected_rows > 0) {
                    $success_message = "Document sent to embassy successfully!";
                    // Refresh the data after update
                    $stmt = $mysqli->prepare("SELECT u.*, o.name as embassy_name 
                                            FROM uploads u 
                                            LEFT JOIN organizations o ON u.embassy_id = o.id 
                                            WHERE u.id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $data = $result->fetch_assoc();
                } else {
                    $error_message = "No rows were updated. Please check if the ID exists.";
                }
            } else {
                $error_message = "Error executing update: " . $updateStmt->error;
            }
        }
    }
}

// Always fetch the data using the ID
$stmt = $mysqli->prepare("SELECT u.*, o.name as embassy_name 
                         FROM uploads u 
                         LEFT JOIN organizations o ON u.embassy_id = o.id 
                         WHERE u.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    $error_message = "❌ No record found for this ID.";
} else {
    // Handle multiple file URLs
    $file_urls = !empty($data['file_url']) ? explode(',', $data['file_url']) : [];
}

// Fetch embassy organizations
$embassies = $mysqli->query("SELECT id, name FROM organizations WHERE domain = 'Embassy' AND status = 'active' ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>File Upload </title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <!-- [Favicon] icon -->
    <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon">
    <!-- [Page specific CSS] start -->
    <!-- fileupload-custom css -->
    <link rel="stylesheet" href="../assets/css/plugins/dropzone.min.css">
    <!-- [Page specific CSS] end -->
    <!-- [Google Font] Family -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="../assets/fonts/tabler/tabler-icons.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="../assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="../assets/css/style-preset.css">
</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Header Topbar ] start -->
    <header class="pc-header">
        <div class="header-wrapper">
            <!-- [Mobile Media Block] start -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- ======= Menu collapse Icon ===== -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>

                </ul>
            </div>
            <!-- [Mobile Media Block end] -->
        </div>
    </header>
    <!-- [ Header ] end -->
    <!-- [ Main Content ] start -->
    <section class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Forms</a></li>
                                <li class="breadcrumb-item" aria-current="page">File Upload</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">File Details</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- [ Form Validation ] start -->
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Form Validation</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success_message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($success_message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error_message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Requesting For:</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly class="form-control"
                                        value="<?= htmlspecialchars(ucfirst(str_replace('_', ' ', $data['requesting_for'] ?? 'myself'))) ?>">
                                </div>
                            </div>
                            <?php if (($data['requesting_for'] ?? '') === 'someone_else' && !empty($data['beneficiary_name'])): ?>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Beneficiary Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly class="form-control"
                                        value="<?= htmlspecialchars($data['beneficiary_name'] ?? '') ?>">
                                </div>
                            </div>
                            <?php if (!empty($data['beneficiary_dob'])): ?>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Beneficiary Date of Birth:</label>
                                <div class="col-sm-9">
                                    <input type="date" readonly class="form-control"
                                        value="<?= htmlspecialchars($data['beneficiary_dob'] ?? '') ?>">
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Requestor Name:</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly class="form-control"
                                        value="<?= htmlspecialchars($data['name'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Date Requested:</label>
                                <div class="col-sm-9">
                                    <input type="date" readonly class="form-control"
                                        value="<?= htmlspecialchars($data['date'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Email:</label>
                                <div class="col-sm-9">
                                    <input type="email" readonly class="form-control"
                                        value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Telephone:</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly class="form-control"
                                        value="<?= htmlspecialchars($data['phonenumber'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Uploaded Files:</label>
                                <div class="col-sm-9">
                                    <?php if (!empty($file_urls)): ?>
                                        <ul class="list-group">
                                            <?php foreach ($file_urls as $url): ?>
                                                <li class="list-group-item">
                                                    <i class="ti ti-file-text me-2"></i><?= htmlspecialchars(basename(trim($url))) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p>No files uploaded</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Status:</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly class="form-control"
                                        value="<?= htmlspecialchars($data['Status'] ?? '') ?>">
                                </div>
                            </div>
                            <?php if (($data['Status'] ?? '') === 'With Embassy' && !empty($data['embassy_name'])): ?>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Sent to Embassy:</label>
                                <div class="col-sm-9">
                                    <input type="text" readonly class="form-control"
                                        value="<?= htmlspecialchars($data['embassy_name']) ?>">
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (($data['Status'] ?? '') === 'With Bank'): ?>
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Send to Embassy:</label>
                                <div class="col-sm-9">
                                    <form method="POST" action="view_doc?id=<?php echo $id; ?>" class="needs-validation" novalidate>
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <select class="form-select" name="embassy_id" required>
                                            <option value="">Select an embassy...</option>
                                            <?php foreach ($embassies as $embassy): ?>
                                            <option value="<?= htmlspecialchars($embassy['id']) ?>">
                                                <?= htmlspecialchars($embassy['name']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select an embassy.
                                        </div>
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary">Send to Embassy</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- [ file-upload ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </section>
    <!-- [ Main Content ] end -->

    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col-sm my-1">
                    <p class="m-0">DocuPura &#9829; <a href="https://themeforest.net/user/codedthemes"
                            target="_blank">Copyright</a></p>
                </div>
                <div class="col-auto my-1">
                    <ul class="list-inline footer-link mb-0">
                        <li class="list-inline-item"><a href="">Home</a></li>
                        <li class="list-inline-item"><a href="" target="_blank">Documentation</a></li>
                        <li class="list-inline-item"><a href="#" target="_blank">Support</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer> <!-- Required Js -->
    <script src="assets/js/plugins/popper.min.js"></script>
    <script src="assets/js/plugins/simplebar.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/fonts/custom-font.js"></script>
    <script src="assets/js/pcoded.js"></script>
    <script src="assets/js/plugins/feather.min.js"></script>
    <!-- [Page Specific JS] start -->
    <!-- file-upload Js -->
    <script src="assets/js/plugins/dropzone-amd-module.min.js"></script>
    <!-- [Page Specific JS] end -->
    <script>
document.addEventListener('DOMContentLoaded', function() {
    var today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(function(input) {
        if (!input.readOnly) {
            input.max = today;
            input.addEventListener('change', function() {
                if (this.value > today) {
                    alert('You cannot select a future date.');
                    this.value = '';
                }
            });
        }
    });
});
</script>
</body>
<!-- [Body] end -->

</html>

<?php include 'sidebar.php'; ?>