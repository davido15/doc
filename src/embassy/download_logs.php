<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['organization_type'] !== 'Embassy') {
    header("Location: ../login.php");
    exit();
}

require_once '../functions/db.php';

// Get download logs for this embassy's documents
$stmt = $mysqli->prepare("
    SELECT dl.*, u.name as document_name
    FROM download_logs dl
    JOIN uploads u ON dl.file_id = u.id
    WHERE u.embassy_id = ?
    ORDER BY dl.download_time DESC
");
$stmt->bind_param("i", $_SESSION['organization_id']);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Download Logs - PDF Verifier</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="https://dm94i2ou1bmfz.cloudfront.net/images/favicon.ico">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/plugins/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="https://dm94i2ou1bmfz.cloudfront.net/css/style-preset.css">
  </head>
  <body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <div class="loader-bg">
      <div class="loader-track">
        <div class="loader-fill"></div>
      </div>
    </div>
    <section class="pc-container">
      <div class="pc-content">
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="page-header-title">
                  <h5 class="m-b-10">Download Logs</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                  <li class="breadcrumb-item">Download Logs</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xl-12">
            <div class="card">
              <div class="card-header">
                <h5>Document Download History</h5>
              </div>
              <div class="card-body table-border-style">
                <div class="table-responsive">
                  <table class="table table-hover" id="download-logs-table">
                    <thead>
                      <tr>
                        <th>Document Name</th>
                        <th>Uploader Email</th>
                        <th>Downloader Email</th>
                        <th>Download Time</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($row['document_name']); ?></td>
                          <td><?php echo htmlspecialchars($row['uploader_email']); ?></td>
                          <td><?php echo htmlspecialchars($row['downloader_email']); ?></td>
                          <td><?php echo date('M d, Y H:i:s', strtotime($row['download_time'])); ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
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
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/popper.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/simplebar.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/vendor-all.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/bootstrap.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/pcoded.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/fonts/custom-font.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/pcoded.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/feather.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/jquery.dataTables.min.js"></script>
    <script src="https://dm94i2ou1bmfz.cloudfront.net/js/plugins/dataTables.bootstrap5.min.js"></script>
    <script>
      $(document).ready(function() {
        $('#download-logs-table').DataTable({
          order: [[3, 'desc']], // Sort by download time by default
          pageLength: 25,
          language: {
            searchPlaceholder: 'Search logs...',
            sSearch: '',
            lengthMenu: '_MENU_',
          }
        });
      });
    </script>
    <?php include 'sidebar.php'; ?>
  </body>
</html> 