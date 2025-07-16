<?php require_once 'all_verifications_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Verifications - PDF Verifier</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="../assets/css/style-preset.css">
    <style>
        .verification-section {
            display: none;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <?php include 'sidebar.php'; ?>
    <section class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">All Verifications</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="view_doc.php?id=<?php echo $doc_id; ?>">View Document</a></li>
                                <li class="breadcrumb-item">All Verifications</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Document: <?php echo htmlspecialchars($document['name']); ?></h5>
                        </div>
                        <div class="card-body">
                            <!-- Verification Buttons -->
                            <form method="POST" id="verification-form">
                                <button type="submit" name="check_integrity" class="btn btn-primary verification-btn">Check Document Activity</button>
                                <button type="submit" name="verify_integrity" class="btn btn-primary verification-btn">Verify Document Content</button>
                                <a href="verify.php?id=<?php echo $doc_id; ?>" class="btn btn-primary">Verify Document Lifecycle</a>
                            </form>
                            
                            <!-- Loader -->
                            <div id="loader" class="loader" style="display: none;"></div>

                            <!-- Results -->
                            <div id="verification-results" class="mt-4">
                                <!-- Document Activity Check Result -->
                                <?php if ($integrity_result): ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Document Activity Check</h5>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($integrity_result['has_upload'] && $integrity_result['has_download']): ?>
                                                <div class="alert alert-success">
                                                    <i class="ti ti-shield-check me-1"></i> Document Activity Verified
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-warning">
                                                    <i class="ti ti-alert-triangle me-1"></i> Document Activity Warning
                                                    <?php if (!$integrity_result['has_upload']): ?>
                                                        <p><strong>Upload:</strong> Not verified</p>
                                                    <?php endif; ?>
                                                    <?php if (!$integrity_result['has_download']): ?>
                                                        <p><strong>Download:</strong> Not verified</p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <!-- Document Content Verification Result -->
                                <?php if ($isIntegrityMaintained !== null): ?>
                                    <div class="card mt-4">
                                        <div class="card-header">
                                            <h5>Document Content Verification</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert <?php echo $isIntegrityMaintained ? 'alert-success' : 'alert-danger'; ?>">
                                                <h6 class="alert-heading">
                                                    <?php if ($isIntegrityMaintained): ?>
                                                        <i class="ti ti-shield-check me-1"></i> Document Content Verified
                                                    <?php else: ?>
                                                        <i class="ti ti-alert-triangle me-1"></i> Document Content Compromised
                                                    <?php endif; ?>
                                                </h6>
                                                <p class="mb-0">
                                                    <?php if (!$isIntegrityMaintained && !empty($debugHashes)): ?>
                                                        <hr>
                                                        <p class="mb-1"><strong>Stored Hash:</strong> <code><?php echo htmlspecialchars($debugHashes['stored']); ?></code></p>
                                                        <p class="mb-0"><strong>New Hash:</strong> <code><?php echo htmlspecialchars($debugHashes['new'] ?? 'Not computed'); ?></code></p>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="../assets/js/plugins/popper.min.js"></script>
    <script src="../assets/js/plugins/simplebar.min.js"></script>
    <script src="../assets/js/vendor-all.min.js"></script>
    <script src="../assets/js/plugins/bootstrap.min.js"></script>
    <script src="../assets/js/pcoded.min.js"></script>
    <script>
        document.querySelectorAll('.verification-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('loader').style.display = 'block';
                document.getElementById('verification-results').style.display = 'none';
            });
        });
        window.onload = function() {
            const resultsContainer = document.getElementById('verification-results');
            if (resultsContainer.children.length > 0) {
                 document.getElementById('loader').style.display = 'none';
            }
        }
    </script>
</body>
</html> 