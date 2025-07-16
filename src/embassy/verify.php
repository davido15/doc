<?php require_once 'verify_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Document Verification Report</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
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
                                <h5 class="m-b-10">Verification Report</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="view_doc.php?id=<?php echo $doc_id; ?>">View Document</a></li>
                                <li class="breadcrumb-item">Verification Report</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Document Lifecycle Verification</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($lifecycleVerification)): ?>
                                <?php if ($lifecycleVerification['is_valid']): ?>
                                    <div class="alert alert-success">
                                        <h6 class="alert-heading"><i class="ti ti-shield-check me-1"></i> Document Lifecycle Verified</h6>
                                        <p>The document has passed all verification checks and its integrity is confirmed.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-danger">
                                        <h6 class="alert-heading"><i class="ti ti-alert-triangle me-1"></i> Document Lifecycle Issues Detected</h6>
                                        <p>The following issues were found:</p>
                                        <ul>
                                            <?php foreach ($lifecycleVerification['issues'] as $issue): ?>
                                                <li><?php echo htmlspecialchars($issue); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                <hr>
                                <h6>Verification Details:</h6>
                                <pre><code><?php echo json_encode($lifecycleVerification, JSON_PRETTY_PRINT); ?></code></pre>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <p>Could not retrieve verification data for this document.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html> 