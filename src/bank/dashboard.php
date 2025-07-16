<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['organization_type'] !== 'Bank') {
    header("Location: ../login");
    exit();
}

require_once '../functions/db.php';

// Get bank's organization ID
$bank_id = $_SESSION['organization_id'];

// Get total documents
$stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM uploads WHERE organization_id = ?");
$stmt->bind_param("i", $bank_id);
$stmt->execute();
$total_docs = $stmt->get_result()->fetch_assoc()['total'];

// Get documents with embassy
$stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM uploads WHERE organization_id = ? AND embassy_id IS NOT NULL");
$stmt->bind_param("i", $bank_id);
$stmt->execute();
$with_embassy = $stmt->get_result()->fetch_assoc()['total'];

// Get documents with bank
$stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM uploads WHERE organization_id = ? AND Status = 'With Bank'");
$stmt->bind_param("i", $bank_id);
$stmt->execute();
$with_bank = $stmt->get_result()->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
  <!-- [Head] start -->
  <head>
    <title>Bank Dashboard - PDF Verifier</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- [Favicon] icon -->
    <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon">
    <!-- [Page specific CSS] start -->
    <!-- fileupload-custom css -->
    <link rel="stylesheet" href="../assets/css/plugins/dropzone.min.css">
    <!-- [Page specific CSS] end -->
    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="../assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="../assets/css/style-preset.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="../assets/css/plugins/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/css/plugins/responsive.bootstrap5.min.css">
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
    <?php include 'sidebar.php'; ?>
    <!-- [ Main Content ] start -->
    <div class="pc-container">
      <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <ul class="breadcrumb">
                                          <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                  <li class="breadcrumb-item">Dashboard</li>
                </ul>
              </div>
              <div class="col-md-12">
                <div class="page-header-title">
                  <h2 class="mb-0">Bank Dashboard</h2>
                </div>
              </div>
            </div>
          </div>
                            </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
          <!-- [ statistics ] start -->
          <div class="col-sm-4">
            <div class="card">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h4 class="text-c-yellow f-w-600"><?php echo $total_docs; ?></h4>
                    <h6 class="text-muted m-b-0">Total Documents</h6>
                                            </div>
                  <div class="col-4 text-end">
                    <i class="feather icon-file-text f-28 text-c-yellow"></i>
                                            </div>
                                        </div>
                                            </div>
                                            </div>
                                        </div>
          <div class="col-sm-4">
            <div class="card">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h4 class="text-c-green f-w-600"><?php echo $with_bank; ?></h4>
                    <h6 class="text-muted m-b-0">With Bank</h6>
                                            </div>
                  <div class="col-4 text-end">
                    <i class="feather icon-briefcase f-28 text-c-green"></i>
                                            </div>
                                        </div>
                                            </div>
                                            </div>
                                        </div>
          <div class="col-sm-4">
            <div class="card">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h4 class="text-c-blue f-w-600"><?php echo $with_embassy; ?></h4>
                    <h6 class="text-muted m-b-0">Sent to Embassy</h6>
                  </div>
                  <div class="col-4 text-end">
                    <i class="feather icon-globe f-28 text-c-blue"></i>
                  </div>
                                </div>
                            </div>
                            </div>
                        </div>
          <!-- [ statistics ] end -->

          <!-- [ Recent uploads ] start -->
          <div class="col-xl-12">
            <div class="card">
              <div class="card-header">
                <h5>Recent Uploads</h5>
              </div>
              <div class="card-body table-border-style">
                <div class="table-responsive">
                  <table class="table table-hover" id="uploads-table">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $stmt = $mysqli->prepare("SELECT u.*, o.name as embassy_name,
                                            CASE 
                                                WHEN u.requesting_for = 'myself' THEN u.name
                                                WHEN u.requesting_for = 'someone_else' THEN CONCAT(u.name, ' (for: ', u.beneficiary_name, ')')
                                                ELSE u.name
                                            END as display_name
                                            FROM uploads u 
                                            LEFT JOIN organizations o ON u.embassy_id = o.id 
                                            WHERE u.organization_id = ? 
                                            ORDER BY u.date DESC 
                                            LIMIT 10");
                      $stmt->bind_param("i", $bank_id);
                      $stmt->execute();
                      $result = $stmt->get_result();
                      while ($row = $result->fetch_assoc()) {
                          $status = htmlspecialchars($row['Status']);
                          $embassy_name = $row['embassy_name'] ? " (" . htmlspecialchars($row['embassy_name']) . ")" : "";
                          echo "<tr>";
                          echo "<td>" . htmlspecialchars($row['display_name']) . "</td>";
                          echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                          echo "<td>" . htmlspecialchars($row['phonenumber']) . "</td>";
                          echo "<td>" . date('M d, Y', strtotime($row['date'])) . "</td>";
                          echo "<td>" . $status . $embassy_name . "</td>";
                          echo "<td>
                                  <a href='view_doc?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>View</a>
                                </td>";
                          echo "</tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                                    </div>
                                    </div>
                                </div>
                            </div>
          <!-- [ Recent uploads ] end -->
        </div>
        <!-- [ Main Content ] end -->
                                </div>
                            </div>
    <!-- [ Main Content ] end -->
    <footer class="pc-footer">
      <div class="footer-wrapper container-fluid">
        <div class="row">
          <div class="col-sm my-1">
            <p class="m-0"
              >DocuPura &#9829;  <a href="https://themeforest.net/user/codedthemes" target="_blank">Copyright</a></p
            >
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
    <!-- [Page Specific JS] start -->
    <!-- file-upload Js -->
    <script src="../assets/js/plugins/dropzone-amd-module.min.js"></script>
    <!-- [Page Specific JS] end -->
    <script>
      $(document).ready(function() {
        $('#uploads-table').DataTable({
          responsive: true,
          language: {
            searchPlaceholder: 'Search by name, email, phone, or verification code...',
            sSearch: '',
            lengthMenu: '_MENU_',
          },
          pageLength: 10,
          order: [[3, 'desc']], // Sort by date column by default
          columnDefs: [
            { orderable: false, targets: [6] } // Disable sorting on actions column
          ]
        });
      });
    </script>
  </body>
  <!-- [Body] end -->
</html> 