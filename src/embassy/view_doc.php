<?php require_once 'view_doc_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
  <!-- [Head] start -->
  <head>
    <title>View Document - PDF Verifier</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- [Favicon] icon -->
    <link rel="shortcut icon" href="../assets/images/favicon.ico">
    <!-- [Page specific CSS] start -->
    <!-- fileupload-custom css -->
    <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon">
    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="../assets/css/plugins/dataTables.bootstrap5.min.css">
    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
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
    <!-- [ Main Content ] start -->
    <section class="pc-container">
      <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="page-header-title">
                  <h5 class="m-b-10">View Document</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                  <li class="breadcrumb-item">View Document</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
          <div class="col-xl-12">
            <div class="card">
              <div class="card-header">
                <h5>Document Details</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Requesting For</label>
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $document['requesting_for'] ?? 'myself'))); ?>" readonly>
                    </div>
                    <?php if (($document['requesting_for'] ?? '') === 'someone_else' && !empty($document['beneficiary_name'])): ?>
                    <div class="mb-3">
                      <label class="form-label">Beneficiary Name</label>
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($document['beneficiary_name'] ?? ''); ?>" readonly>
                    </div>
                    <?php if (!empty($document['beneficiary_dob'])): ?>
                    <div class="mb-3">
                      <label class="form-label">Beneficiary Date of Birth</label>
                      <input type="date" class="form-control" value="<?php echo htmlspecialchars($document['beneficiary_dob'] ?? ''); ?>" readonly>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    <div class="mb-3">
                      <label class="form-label">Requestor Name</label>
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($document['name']); ?>" readonly>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Email</label>
                      <input type="email" class="form-control" value="<?php echo htmlspecialchars($document['email']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Phone</label>
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($document['phonenumber']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Date</label>
                      <input type="text" class="form-control" value="<?php echo date('M d, Y', strtotime($document['date'])); ?>" readonly>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Status</label>
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($document['Status']); ?>" readonly>
                    </div>
                  </div>
                </div>
                <!-- Document Files Section -->
                <div class="row mt-2">
                  <div class="col-12">
                    <h6>Document Files</h6>
                    <?php if (!$is_verified): ?>
                    <div class="alert alert-warning">
                      <h6 class="alert-heading">Verification Required</h6>
                      <p>Please enter the verification code to view the documents.</p>
                      <form method="POST" action="view_doc?id=<?php echo urlencode($doc_id); ?>" class="mt-3">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="input-group">
                              <input type="text" name="verification_code" class="form-control" placeholder="Enter verification code" required>
                              <button type="submit" name="verify_code" class="btn btn-primary">Verify</button>
                            </div>
                            <?php if (isset($error)): ?>
                              <div class="text-danger mt-2"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </form>
                    </div>
                    <?php else: ?>
                    <div class="list-group">
                      <?php
                      $file_urls = explode(',', $document['file_url']);
                      foreach ($file_urls as $url) {
                          $url = trim($url);
                          if (!empty($url)) {
                              echo '<a href="download?key=' . htmlspecialchars(urlencode($url)) . '&code=' . htmlspecialchars($document['verification_code']) . '" class="list-group-item list-group-item-action" target="_blank">';
                              echo '<i class="ti ti-file-text me-2"></i>' . basename($url);
                              echo '</a>';
                          }
                      }
                      ?>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
                <!-- End Document Files Section -->

                <!-- Document Content Verification Section -->
                <?php if ($is_verified): ?>
                <div class="row mt-2">
                  <div class="col-12">
                    <h6>Document Content Verification</h6>
                    <form method="POST" action="verify_document_content.php" class="d-inline">
                      <input type="hidden" name="document_id" value="<?php echo $doc_id; ?>">
                      <button type="submit" name="verify_content" class="btn btn-warning">
                        <i class="ti ti-shield-check me-1"></i>
                        Verify Document Content
                      </button>
                    </form>
                    
                    <?php if (isset($_SESSION['verification_result']) && $_SESSION['verification_result']['document_id'] == $doc_id): ?>
                      <div class="mt-3">
                        <?php 
                        $result = $_SESSION['verification_result'];
                        if ($result['integrity_check']['isValid']): ?>
                          <div class="alert alert-success">
                            <i class="ti ti-check me-2"></i>
                            <strong>Document Integrity: PASS</strong><br>
                            Document content has not been tampered with.<br>

                            <small>Verified on: <?php echo $result['verification_time']; ?></small>
                          </div>
                        <?php else: ?>
                          <div class="alert alert-danger">
                            <i class="ti ti-alert-triangle me-2"></i>
                            <strong>Document Integrity: FAIL</strong><br>
                            Document content may have been compromised.<br>
                            <strong>Stored Hash:</strong> <?php echo $result['integrity_check']['storedHash']; ?><br>
                            <strong>Computed Hash:</strong> <?php echo $result['integrity_check']['computedHash']; ?><br>
                            <small>Verified on: <?php echo $result['verification_time']; ?></small>
                          </div>
                        <?php endif; ?>
                      </div>
                      <?php 
                      // Clear the verification result from session
                      unset($_SESSION['verification_result']);
                      ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['error'])): ?>
                      <div class="mt-3">
                        <div class="alert alert-danger">
                          <i class="ti ti-x me-2"></i>
                          Error: <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
                <!-- End Document Content Verification Section -->
                <?php endif; ?>





                <?php if ($document['Status'] === 'Pending'): ?>
                <div class="row mt-2">
                  <div class="col-12">
                    <form method="POST" class="d-inline">
                      <button type="submit" name="verify" class="btn btn-success">
                        <i class="ti ti-check me-1"></i>
                        Verify Document
                      </button>
                    </form>
                  </div>
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
      </div>
    </section>
    <!-- [ Main Content ] end -->

    <!-- Document History and Logs Section -->
    <section class="pc-container">
      <div class="pc-content">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="ti ti-history me-2"></i>
                  Document History & Logs
                </h5>
              </div>
              <div class="card-body">
                <!-- Document Integrity Verification History Card -->
                <div class="row">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header">
                        <h6 class="card-title mb-0">
                          <i class="ti ti-shield-check me-2"></i>
                          Document Integrity Verification History
                        </h6>
                      </div>
                      <div class="card-body">
                        <div class="table-responsive">
                          <table class="table table-hover">
                            <thead>
                              <tr>
                                <th>Verification Date</th>
                                <th>Performed By</th>
                                <th>Result</th>
                                <th>Stored Hash</th>
                                <th>Computed Hash</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              // Get integrity verification logs for this document
                              $stmt = $mysqli->prepare("
                                  SELECT * FROM document_verifications 
                                  WHERE document_id = ? AND action IN ('integrity_pass', 'integrity_fail')
                                  ORDER BY timestamp DESC
                              ");
                              $stmt->bind_param("i", $doc_id);
                              $stmt->execute();
                              $integrity_logs_result = $stmt->get_result();
                              
                              // Debug: Log the query results
                              error_log("Integrity verification query - Document ID: $doc_id, Found rows: " . $integrity_logs_result->num_rows);
                              
                              if ($integrity_logs_result->num_rows > 0):
                                while ($integrity_log = $integrity_logs_result->fetch_assoc()):
                                  // Determine result based on whether stored and computed hashes match
                                  $storedHash = $integrity_log['digital_signature'];
                                  $computedHash = $integrity_log['salt'];
                                  $result = ($storedHash === $computedHash) ? 'PASS' : 'FAIL';
                                  $resultClass = $result === 'PASS' ? 'success' : 'danger';
                              ?>
                                <tr>
                                  <td><?php echo date('M d, Y H:i:s', $integrity_log['timestamp']); ?></td>
                                  <td><?php echo htmlspecialchars($integrity_log['email']); ?></td>
                                  <td>
                                    <span class="badge bg-<?php echo $resultClass; ?>"><?php echo $result; ?></span>
                                  </td>
                                  <td>
                                    <code class="text-muted" style="font-size: 0.8em;">
                                      <?php echo substr($storedHash, 0, 16) . '...'; ?>
                                    </code>
                                  </td>
                                  <td>
                                    <code class="text-muted" style="font-size: 0.8em;">
                                      <?php echo substr($computedHash, 0, 16) . '...'; ?>
                                    </code>
                                  </td>
                                </tr>
                              <?php 
                                endwhile;
                              else:
                              ?>
                                <tr>
                                  <td colspan="5" class="text-center text-muted">
                                    <i class="ti ti-info-circle me-1"></i>
                                    No integrity verifications performed yet
                                  </td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- End Document Integrity Verification History Card -->

                <!-- Document Activity Log Card -->
                <div class="row mt-4">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header">
                        <h6 class="card-title mb-0">
                          <i class="ti ti-list me-2"></i>
                          Document Activity Log
                        </h6>
                      </div>
                      <div class="card-body">
                        <div class="table-responsive">
                          <table class="table table-hover">
                            <thead>
                              <tr>
                                <th>Action</th>
                                <th>Email</th>
                                <th>Time</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              // Get all logs for this document (excluding integrity checks)
                              $stmt = $mysqli->prepare("
                                  SELECT * FROM document_verifications 
                                  WHERE document_id = ? AND action NOT IN ('integrity_pass', 'integrity_fail')
                                  ORDER BY timestamp DESC
                              ");
                              $stmt->bind_param("i", $doc_id);
                              $stmt->execute();
                              $logs_result = $stmt->get_result();
                              while ($log = $logs_result->fetch_assoc()):
                              ?>
                                <tr>
                                  <td>
                                    <?php 
                                    $actionClass = 'secondary';
                                    $actionText = ucfirst($log['action']);
                                    
                                    if ($log['action'] === 'upload') {
                                        $actionClass = 'primary';
                                    } elseif ($log['action'] === 'download') {
                                        $actionClass = 'success';
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $actionClass; ?>"><?php echo $actionText; ?></span>
                                  </td>
                                  <td><?php echo htmlspecialchars($log['email']); ?></td>
                                  <td><?php echo date('M d, Y H:i:s', $log['timestamp']); ?></td>
                                </tr>
                              <?php endwhile; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- End Document Activity Log Card -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Document History and Logs Section -->
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
              <li class="list-inline-item"><a href="">Home</a></li>
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
<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/js/plugins/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>
<script src="../assets/js/fonts/custom-font.js"></script>
<script src="../assets/js/pcoded.js"></script>
<script src="../assets/js/plugins/feather.min.js"></script>
    <!-- [Page Specific JS] start -->
    <!-- file-upload Js -->
    <script src="../assets/js/plugins/dropzone-amd-module.min.js"></script>
    <script src="../assets/js/plugins/jquery.dataTables.min.js"></script>
    <script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
    <script>
      // [ Zero Configuration ] start
      $('#simpletable').DataTable();
      // [ Default Ordering ] start
      $('#order-table').DataTable({
        order: [[3, 'desc']]
      });
      // [ Multi-Column Ordering ]
      $('#multi-colum-dt').DataTable({
        columnDefs: [
          {
            targets: [0],
            orderData: [0, 1]
          },
          {
            targets: [1],
            orderData: [1, 0]
          },
          {
            targets: [4],
            orderData: [4, 0]
          }
        ]
      });
      // [ Complex Headers ]
      $('#complex-dt').DataTable();
      // [ DOM Positioning ]
      $('#DOM-dt').DataTable({
        dom: '<"top"i>rt<"bottom"flp><"clear">'
      });
      // [ Alternative Pagination ]
      $('#alt-pg-dt').DataTable({
        pagingType: 'full_numbers'
      });
      // [ Scroll - Vertical ]
      $('#scr-vrt-dt').DataTable({
        scrollY: '200px',
        scrollCollapse: true,
        paging: false
      });
      // [ Scroll - Vertical, Dynamic Height ]
      $('#scr-vtr-dynamic').DataTable({
        scrollY: '50vh',
        scrollCollapse: true,
        paging: false
      });
      // [ Language - Comma Decimal Place ]
      $('#lang-dt').DataTable({
        language: {
          decimal: ',',
          thousands: '.'
        }
      });
      layout_change('light');
      change_box_container('false');
      layout_rtl_change('false');
      preset_change("preset-1");
      font_change("Public-Sans");
    </script>
    <!-- [Page Specific JS] end -->
    <!-- Add the following line to include the sidebar within the HTML structure -->
    <?php include 'sidebar.php'; ?>
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
