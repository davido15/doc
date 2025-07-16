<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['organization_id'] != 1) {
    header("Location: logout.php");
    exit();
}
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
                  <h2 class="mb-0">File Upload</h2>
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
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= urldecode($_GET['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= urldecode($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
  <form id="validate-me" action="../s3upload/upload" method="POST" enctype="multipart/form-data" class="validate-me">
  <div class="form-group row">
    <label class="col-lg-4 col-form-label text-lg-end">Requesting For</label>
    <div class="col-lg-6">
      <select class="form-control" name="requesting_for" id="requesting_for" required>
        <option value="">Select who you're requesting for</option>
        <option value="myself">Myself</option>
        <option value="someone_else">Someone Else</option>
      </select>
      <small class="form-text text-muted">Choose whether this request is for you or someone else</small>
    </div>
  </div>
  <div class="form-group row" id="beneficiary_row" style="display: none;">
    <label class="col-lg-4 col-form-label text-lg-end">Beneficiary Name</label>
    <div class="col-lg-6">
      <input type="text" class="form-control" name="beneficiary_name" id="beneficiary_name">
      <small class="form-text text-muted">Enter the name of the person you're requesting for</small>
    </div>
  </div>
  <div class="form-group row" id="beneficiary_dob_row" style="display: none;">
    <label class="col-lg-4 col-form-label text-lg-end">Beneficiary Date of Birth</label>
    <div class="col-lg-6">
      <input type="date" class="form-control" name="beneficiary_dob" id="beneficiary_dob" max="<?php echo date('Y-m-d'); ?>">
      <small class="form-text text-muted">Enter the date of birth of the person you're requesting for</small>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-lg-4 col-form-label text-lg-end">Requestor Name</label>
    <div class="col-lg-6">
      <input type="text" class="form-control" name="name" required>
      <small class="form-text text-muted">Your name (the person making the request)</small>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-lg-4 col-form-label text-lg-end">Date Requested:</label>
    <div class="col-lg-6">
      <input type="date" class="form-control" name="date" required max="<?php echo date('Y-m-d'); ?>">
      <small class="form-text text-muted">YYYY-MM-DD</small>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-lg-4 col-form-label text-lg-end">Email:</label>
    <div class="col-lg-6">
      <input type="email" name="email" class="form-control" required>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-lg-4 col-form-label text-lg-end">Telephone Number</label>
    <div class="col-lg-6">
      <input type="text" class="form-control" name="phonenumber"  required>
      <small class="form-text text-muted">123-456-7890</small>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-lg-4 col-form-label text-lg-end">Select File</label>
    <div class="col-lg-6">
      <input type="file" name="file[]" multiple class="form-control" required>
    </div>
  </div>
  <!-- Hidden fields -->
  <input type="hidden" name="organization_id" value="<?php echo $_SESSION['organization_id']; ?>">
  <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
  <input type="hidden" name="bank_user_email" value="<?php echo $_SESSION['email']; ?>">
  <div class="text-center m-t-20">
    <button class="btn btn-primary" type="submit">Submit</button>
  </div>
</form>
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
    </footer> <!-- Required Js -->
<script src="../assets/js/plugins/popper.min.js"></script>
<script src="../assets/js/plugins/simplebar.min.js"></script>
<script src="../assets/js/plugins/bootstrap.min.js"></script>
<script src="../assets/js/fonts/custom-font.js"></script>
<script src="../assets/js/pcoded.js"></script>
<script src="../assets/js/plugins/feather.min.js"></script>
    <!-- [Page Specific JS] start -->
    <!-- file-upload Js -->
    <script src="../assets/js/plugins/dropzone-amd-module.min.js"></script>
    <!-- [Page Specific JS] end -->
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const requestingForSelect = document.getElementById('requesting_for');
        const beneficiaryRow = document.getElementById('beneficiary_row');
        const beneficiaryDobRow = document.getElementById('beneficiary_dob_row');
        const beneficiaryNameInput = document.getElementById('beneficiary_name');
        const beneficiaryDobInput = document.getElementById('beneficiary_dob');
        
        requestingForSelect.addEventListener('change', function() {
            if (this.value === 'someone_else') {
                beneficiaryRow.style.display = 'flex';
                beneficiaryDobRow.style.display = 'flex';
                beneficiaryNameInput.required = true;
                beneficiaryDobInput.required = true;
            } else {
                beneficiaryRow.style.display = 'none';
                beneficiaryDobRow.style.display = 'none';
                beneficiaryNameInput.required = false;
                beneficiaryDobInput.required = false;
                beneficiaryNameInput.value = '';
                beneficiaryDobInput.value = '';
            }
        });
        // Set max date for all date inputs to today
        var today = new Date().toISOString().split('T')[0];
        document.querySelectorAll('input[type="date"]').forEach(function(input) {
            input.max = today;
            input.addEventListener('change', function() {
                if (this.value > today) {
                    alert('You cannot select a future date.');
                    this.value = '';
                }
            });
        });
    });
    </script>
  </body>
  <!-- [Body] end -->
</html>
