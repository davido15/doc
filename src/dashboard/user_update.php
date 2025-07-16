<?php
session_start();

// Session check for organization ID = 0
if (!isset($_SESSION['user_id']) || $_SESSION['organization_id'] != 0) {
    header("Location: /logout");
    exit();
}

require_once '../functions/db.php';  // Updated path
require_once '../functions/logger.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $organizationId = $_POST['organization_id'];
    $status = $_POST['status'];

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email)) {
        $message = "First name, last name, and email are required fields.";
        $messageType = "danger";
    } else {
        // Check if email exists for other users
        $checkEmail = $mysqli->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $checkEmail->bind_param("si", $email, $userId);
        $checkEmail->execute();
        $checkEmail->store_result();
        
        if ($checkEmail->num_rows > 0) {
            $message = "Email already exists for another user.";
            $messageType = "danger";
        } else {
            // Update user
            $stmt = $mysqli->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, organization_id = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $firstName, $lastName, $email, $phone, $organizationId, $status, $userId);
            
            if ($stmt->execute()) {
                $message = "User updated successfully!";
                $messageType = "success";
            } else {
                $message = "Error updating user: " . $mysqli->error;
                $messageType = "danger";
            }
        }
    }
}

// Fetch all users
$users = $mysqli->query("
    SELECT u.*, o.name as organization_name 
    FROM users u 
    LEFT JOIN organizations o ON u.organization_id = o.id 
    ORDER BY u.id DESC
")->fetch_all(MYSQLI_ASSOC);

// Fetch organizations for dropdown
$organizations = $mysqli->query("SELECT id, name FROM organizations ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update User</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <link rel="stylesheet" href="../assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="../assets/css/style-preset.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="../assets/css/plugins/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/css/plugins/responsive.bootstrap5.min.css">
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
                                <h5 class="m-b-10">Update User</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                                <li class="breadcrumb-item">Users</li>
                                <li class="breadcrumb-item" aria-current="page">Update User</li>
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
                                <table class="table table-hover">
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
                                            <td><?= htmlspecialchars($user['organization_name']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>">
                                                    <?= ucfirst(htmlspecialchars($user['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal<?= $user['id'] ?>">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal<?= $user['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit User</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">First Name</label>
                                                                <input type="text" class="form-control" name="first_name" 
                                                                       value="<?= htmlspecialchars($user['first_name']) ?>" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Last Name</label>
                                                                <input type="text" class="form-control" name="last_name" 
                                                                       value="<?= htmlspecialchars($user['last_name']) ?>" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Email</label>
                                                                <input type="email" class="form-control" name="email" 
                                                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Phone</label>
                                                                <input type="tel" class="form-control" name="phone" 
                                                                       value="<?= htmlspecialchars($user['phone']) ?>">
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Organization</label>
                                                                <select class="form-select" name="organization_id" required>
                                                                    <?php foreach ($organizations as $org): ?>
                                                                    <option value="<?= $org['id'] ?>" 
                                                                            <?= $org['id'] == $user['organization_id'] ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($org['name']) ?>
                                                                    </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select class="form-select" name="status" required>
                                                                    <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                                    <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
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
        </div>
    </div>

    <!-- Required Js -->
    <script src="../assets/js/plugins/popper.min.js"></script>
    <script src="../assets/js/plugins/simplebar.min.js"></script>
    <script src="../assets/js/plugins/bootstrap.min.js"></script>
    <script src="../assets/js/fonts/custom-font.js"></script>
    <script src="../assets/js/pcoded.js"></script>
    <script src="../assets/js/plugins/feather.min.js"></script>
    <!-- DataTables JS -->
    <script src="../assets/js/plugins/jquery.dataTables.min.js"></script>
    <script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
    <script src="../assets/js/plugins/dataTables.responsive.min.js"></script>
    <script src="../assets/js/plugins/responsive.bootstrap5.min.js"></script>
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
    <?php include 'footer.php' ?>
</body>
</html> 