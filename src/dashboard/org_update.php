<?php
session_start();

// Session check for organization ID = 0
if (!isset($_SESSION['user_id']) || $_SESSION['organization_id'] != 0) {
    header("Location: /logout");
    exit();
}

require_once '../functions/db.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orgId = $_POST['org_id'];
    $name = $_POST['name'];
    $domain = $_POST['domain'];
    $status = $_POST['status'];

    // Validate required fields
    if (empty($name) || empty($domain)) {
        $message = "Name and domain are required fields.";
        $messageType = "danger";
    } else {
        // Check if domain exists for other organizations
        $checkDomain = $mysqli->prepare("SELECT id FROM organizations WHERE domain = ? AND id != ?");
        $checkDomain->bind_param("si", $domain, $orgId);
        $checkDomain->execute();
        $result = $checkDomain->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Domain already exists for another organization.";
            $messageType = "danger";
        } else {
            // Update organization
            $stmt = $mysqli->prepare("UPDATE organizations SET name = ?, domain = ?, status = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $domain, $status, $orgId);
            
            if ($stmt->execute()) {
                $message = "Organization updated successfully!";
                $messageType = "success";
            } else {
                $message = "Error updating organization: " . $mysqli->error;
                $messageType = "danger";
            }
        }
    }
}

// Fetch all organizations
$organizations = $mysqli->query("SELECT * FROM organizations ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Organization</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style-preset.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/plugins/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/plugins/responsive.bootstrap5.min.css">
</head>

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    
    <?php include 'header.php' ?>
    <?php include 'sidebar.php' ?>
    
    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Update Organization</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                                <li class="breadcrumb-item">Organizations</li>
                                <li class="breadcrumb-item" aria-current="page">Update Organization</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($message): ?>
                            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                                <?= $message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Domain</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($organizations as $org): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($org['id']) ?></td>
                                            <td><?= htmlspecialchars($org['name']) ?></td>
                                            <td><?= htmlspecialchars($org['domain']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $org['status'] === 'active' ? 'success' : 'danger' ?>">
                                                    <?= ucfirst(htmlspecialchars($org['status'])) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($org['created_at']) ?></td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal<?= $org['id'] ?>">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal<?= $org['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Organization</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="org_id" value="<?= $org['id'] ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Name</label>
                                                                <input type="text" class="form-control" name="name" 
                                                                       value="<?= htmlspecialchars($org['name']) ?>" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Domain</label>
                                                                <input type="text" class="form-control" name="domain" 
                                                                       value="<?= htmlspecialchars($org['domain']) ?>" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select class="form-select" name="status" required>
                                                                    <option value="active" <?= $org['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                                    <option value="inactive" <?= $org['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->
    
    <?php include 'footer.php' ?>

    <!-- Required Js -->
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/popper.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/simplebar.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/bootstrap.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/fonts/custom-font.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/pcoded.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/feather.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/jquery.dataTables.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/dataTables.bootstrap5.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/dataTables.responsive.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/responsive.bootstrap5.min.js"></script>
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('.table').DataTable({
                responsive: true,
                language: {
                    search: "Search organizations:",
                    lengthMenu: "Show _MENU_ organizations per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ organizations",
                    infoEmpty: "No organizations available",
                    infoFiltered: "(filtered from _MAX_ total organizations)",
                    zeroRecords: "No matching organizations found",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                order: [[0, 'desc']], // Sort by ID column in descending order
                pageLength: 10, // Show 10 records per page
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Page length options
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip' // Layout
            });
        });
    </script>
    <script>
        layout_change('light');
        change_box_container('false');
        layout_rtl_change('false');
        preset_change("preset-1");
        font_change("Public-Sans");
    </script>
</body>
</html> 