<?php
session_start();

// Session check for organization ID = 0
if (!isset($_SESSION['user_id']) || $_SESSION['organization_id'] != 0) {
    header("Location: /logout");
    exit();
}

require_once '../functions/db.php';  // Updated path

// Fetch uploaded records
$stmt = $mysqli->query("SELECT id, name, email, phonenumber, requesting_for, beneficiary_name, beneficiary_dob, date, file_url, verification_code, status FROM uploads ORDER BY id DESC");
$uploads = $stmt->fetch_all(MYSQLI_ASSOC); // ✅ MySQLi-compatible

// Fetch summary statistics
$summaryStmt = $mysqli->query("
    SELECT
        (SELECT COUNT(*) FROM users) AS total_users,
        (SELECT COUNT(*) FROM uploads) AS total_uploads,
        (SELECT COUNT(*) FROM organizations) AS total_organizations,
        (SELECT COUNT(*) FROM uploads WHERE status = 'with embassy') AS uploads_with_embassy_status
");
$summary = $summaryStmt->fetch_assoc(); // ✅ MySQLi-compatible

// Access summary
$totalUsers = $summary['total_users'];
$totalUploads = $summary['total_uploads'];
$totalOrgs = $summary['total_organizations'];
$uploadsEmbassy = $summary['uploads_with_embassy_status'];
?>

<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>Home</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- [Favicon] icon -->
    <link rel="icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.svg" type="image/x-icon">
    <link rel="shortcut icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.ico">
    <!-- [Google Font] Family -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/plugins/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/plugins/responsive.bootstrap5.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="assets/css/plugins/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="assets/css/plugins/responsive.bootstrap5.min.css">
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
                    <li class="dropdown pc-h-item d-inline-flex d-md-none">
                        <a class="pc-head-link dropdown-toggle arrow-none m-0" data-bs-toggle="dropdown" href="#"
                            role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="ti ti-search"></i>
                        </a>
                        <div class="dropdown-menu pc-h-dropdown drp-search">
                            <form class="px-3">
                                <div class="form-group mb-0 d-flex align-items-center">
                                    <i data-feather="search"></i>
                                    <input type="search" class="form-control border-0 shadow-none"
                                        placeholder="Search here. . .">
                                </div>
                            </form>
                        </div>
                    </li>
                    <li class="pc-h-item d-none d-md-inline-flex">
                        <form class="header-search">
                            <i data-feather="search" class="icon-search"></i>
                            <input type="search" class="form-control" placeholder="Search here. . .">
                        </form>
                    </li>
                </ul>
            </div>
            <!-- [Mobile Media Block end] -->
         
        </div>
    </header>
    <!-- [ Header ] end -->
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
                                <h5 class="m-b-10">Home</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Home</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- [ sample-page ] start -->
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total Organisation</h6>
                            <h4 class="mb-3"><span class="badge bg-light-primary border border-primary"><i
                                        class="ti ti-trending-up"></i> <?= $totalOrgs ?> Active Org</span></h4>
                            <p class="mb-0 text-muted text-sm">Total Partners <span
                                    class="text-primary">Onboarded</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total Users</h6>
                            <h4 class="mb-3"><span class="badge bg-light-success border border-success"><i
                                        class="ti ti-trending-up"></i> <?= $totalUsers ?> Active Users</span></h4>
                            <p class="mb-0 text-muted text-sm">Total count of user <span class="text-success"> in the
                                    system</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total Upload</h6>
                            <h4 class="mb-3"><span class="badge bg-light-warning border border-warning"><i
                                        class="ti ti-trending-down"></i> <?= $totalUploads ?> Successful</span></h4>
                            <p class="mb-0 text-muted text-sm">Total document uploaded <span class="text-warning">in the
                                    system</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Total with Embassy</h6>
                            <h4 class="mb-3"><span class="badge bg-light-info border border-info "><i
                                        class="ti ti-trending-down"></i> <?= $uploadsEmbassy ?> Sent document</span>
                            </h4>
                            <p class="mb-0 text-muted text-sm">Total sent <span class="text-info"> to embassy</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-12">
                    <h5 class="mb-3">All Upload</h5>
                    <div class="card tbl-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="uploads-table" class="table table-hover table-borderless mb-0">
                                    <thead>
                                        <tr>
                                            <th>Record</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Date</th>
                                            <th>Verification Code</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($uploads as $upload): ?>
                                        <tr>
                                            <td>
                                                <a href="view_doc.php?id=<?= urlencode($upload['id']) ?>">
                                                    <?= htmlspecialchars($upload['id']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($upload['name']) ?>
                                                <?php if ($upload['requesting_for'] === 'someone_else' && !empty($upload['beneficiary_name'])): ?>
                                                    <br><small class="text-muted">For: <?= htmlspecialchars($upload['beneficiary_name']) ?>
                                                    <?php if (!empty($upload['beneficiary_dob'])): ?>
                                                        (DOB: <?= htmlspecialchars($upload['beneficiary_dob']) ?>)
                                                    <?php endif; ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($upload['email']) ?></td>
                                            <td><?= htmlspecialchars($upload['phonenumber']) ?></td>
                                            <td><?= htmlspecialchars($upload['date']) ?></td>
                                            <td><?= htmlspecialchars($upload['verification_code']) ?></td>
                                            <td><span class="d-flex align-items-center gap-2"><i
                                                        class="fas fa-circle text-danger f-10 m-r-5"></i><?= htmlspecialchars($upload['status']) ?></span>
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
    <!-- [ Main Content ] end -->
    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col-sm my-1">
                </div>
                <div class="col-auto my-1">
                    <ul class="list-inline footer-link mb-0">
                        <li class="list-inline-item"><a href="../index.html">Home</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <!-- [Page Specific JS] start -->
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/apexcharts.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/pages/dashboard-default.js"></script>
    <!-- [Page Specific JS] end -->
    <!-- Required Js -->
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/popper.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/simplebar.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/vendor-all.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/bootstrap.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/jquery.dataTables.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/dataTables.bootstrap5.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/dataTables.responsive.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/responsive.bootstrap5.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/pcoded.min.js"></script>
    <script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#uploads-table').DataTable({
            responsive: true,
            language: {
                search: "Search records:",
                lengthMenu: "Show _MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ records",
                infoEmpty: "No records available",
                infoFiltered: "(filtered from _MAX_ total records)",
                zeroRecords: "No matching records found",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            order: [[0, 'desc']], // Sort by Record column in descending order
            pageLength: 10, // Show 10 records per page
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Page length options
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip' // Layout
        });
    });
    </script>
    <script>
    layout_change('light');
    </script>
    <script>
    change_box_container('false');
    </script>
    <script>
    layout_rtl_change('false');
    </script>
    <script>
    preset_change("preset-1");
    </script>
    <script>
    font_change("Public-Sans");
    </script>
</body>
<!-- [Body] end -->

</html>