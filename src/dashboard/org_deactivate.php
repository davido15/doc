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
$organizations = [];

// Handle status toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $newStatus = $_POST['current_status'] === 'active' ? 'inactive' : 'active';
    
    $stmt = $mysqli->prepare("UPDATE organizations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $id);
    
    if ($stmt->execute()) {
        $message = "Organization " . ($newStatus === 'active' ? 'activated' : 'deactivated') . " successfully";
        $messageType = 'success';
    } else {
        $message = "Error updating organization status";
        $messageType = 'danger';
    }
}

// Fetch all organizations
$stmt = $mysqli->query("SELECT id, name, domain, status FROM organizations ORDER BY name");
if ($stmt) {
    $organizations = $stmt->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Deactivate Organization</title>
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
                                <h5 class="m-b-10">Manage Organization Status</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                                <li class="breadcrumb-item">Manage Organization Status</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-md-12">
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
                                            <th>Organization Name</th>
                                            <th>Domain</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($organizations as $org): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($org['name']) ?></td>
                                            <td><?= htmlspecialchars($org['domain']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $org['status'] === 'active' ? 'success' : 'danger' ?>">
                                                    <?= ucfirst(htmlspecialchars($org['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <form method="POST" action="" style="display: inline;">
                                                    <input type="hidden" name="id" value="<?= $org['id'] ?>">
                                                    <input type="hidden" name="current_status" value="<?= $org['status'] ?>">
                                                    <button type="submit" class="btn btn-<?= $org['status'] === 'active' ? 'danger' : 'success' ?> btn-sm">
                                                        <?= $org['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
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
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->
    
    <?php include 'footer.php' ?>

    <!-- Required Js -->
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/popper.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/simplebar.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/vendor-all.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/bootstrap.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/pcoded.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/fonts/custom-font.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/pcoded.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/feather.min.js"></script>
    <script>
        layout_change('light');
        change_box_container('false');
        layout_rtl_change('false');
        preset_change("preset-1");
        font_change("Public-Sans");
    </script>
</body>
</html> 