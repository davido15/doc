<?php
session_start();

// Session check for organization ID = 0
if (!isset($_SESSION['user_id']) || $_SESSION['organization_id'] != 0) {
    header("Location: logout.php");
    exit();
}

require_once '../functions/db.php';

$message = '';
$messageType = '';

// Handle status toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    
    // Get current status
    $stmt = $mysqli->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        // Toggle status
        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        
        // Update status
        $updateStmt = $mysqli->prepare("UPDATE users SET status = ? WHERE id = ?");
        $updateStmt->bind_param("si", $newStatus, $userId);
        
        if ($updateStmt->execute()) {
            $message = 'User status updated successfully.';
            $messageType = 'success';
        } else {
            $message = 'Error updating user status: ' . $mysqli->error;
            $messageType = 'danger';
        }
    }
}

// Fetch all users
$stmt = $mysqli->query("SELECT u.*, o.name as organization_name 
                       FROM users u 
                       LEFT JOIN organizations o ON u.organization_id = o.id 
                       ORDER BY u.id DESC");
$users = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Deactivate User</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.svg" type="image/x-icon">
    <link rel="shortcut icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style-preset.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/plugins/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/plugins/responsive.bootstrap5.min.css">
</head>
<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <?php include 'header.php' ?>
    <?php include 'sidebar.php' ?>
    
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Deactivate User</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                                <li class="breadcrumb-item">Users</li>
                                <li class="breadcrumb-item" aria-current="page">Deactivate User</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Organization</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['id']) ?></td>
                                            <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td><?= htmlspecialchars($user['phone']) ?></td>
                                            <td><?= htmlspecialchars($user['organization_name'] ?? 'None') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>">
                                                    <?= ucfirst(htmlspecialchars($user['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <button type="submit" class="btn btn-<?= $user['status'] === 'active' ? 'danger' : 'success' ?> btn-sm">
                                                        <?= $user['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/popper.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/simplebar.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/vendor-all.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/bootstrap.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/pcoded.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/fonts/custom-font.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/pcoded.js"></script>
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
                    search: "Search users:",
                    lengthMenu: "Show _MENU_ users per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ users",
                    infoEmpty: "No users available",
                    infoFiltered: "(filtered from _MAX_ total users)",
                    zeroRecords: "No matching users found",
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
    <img src="https://dm94i2ou1bmfz.cloudfront.net/images/user/avatar-2.jpg" alt="user-image" class="user-avtar">
    <img src="https://dm94i2ou1bmfz.cloudfront.net/images/user/avatar-2.jpg" alt="user-image"
        class="user-avtar wid-35">
    <?php include 'footer.php' ?>
</body>
</html> 