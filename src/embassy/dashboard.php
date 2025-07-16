<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['organization_type'] !== 'Embassy') {
    header("Location: ../login.php");
    exit();
}

require_once '../functions/db.php';

$embassy_id = $_SESSION['organization_id'];

// Fetch document stats
function getDocumentCount($mysqli, $embassy_id, $status = null) {
    if ($status) {
        $stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM uploads WHERE (embassy_id = ? OR embassy_id = 99) AND Status = ?");
        $stmt->bind_param("is", $embassy_id, $status);
    } else {
        $stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM uploads WHERE embassy_id = ? OR embassy_id = 99");
        $stmt->bind_param("i", $embassy_id);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

$total_docs = getDocumentCount($mysqli, $embassy_id);
$verified_docs = getDocumentCount($mysqli, $embassy_id, 'Verified');
$pending_docs = getDocumentCount($mysqli, $embassy_id, 'Pending');

// Handle search
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$result = null;

if (!empty($search_query)) {
    // If a search query is present, filter by verification code (partial match)
    $search_term_like = '%' . $search_query . '%';
    $sql = "SELECT * FROM uploads WHERE (embassy_id = ? OR embassy_id = 99) AND verification_code LIKE ? ORDER BY date DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("is", $embassy_id, $search_term_like);
} else {
    // If no search query, fetch all documents for the embassy and general documents (embassy_id = 99)
    $sql = "SELECT * FROM uploads WHERE embassy_id = ? OR embassy_id = 99 ORDER BY date DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $embassy_id);
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch business verification requests for this embassy
$business_stmt = $mysqli->prepare("SELECT * FROM business_verifications WHERE embassy_id = ? ORDER BY created_at DESC");
$business_stmt->bind_param("i", $embassy_id);
$business_stmt->execute();
$business_verifications = $business_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Embassy Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/plugins/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <script src="../assets/js/plugins/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include 'sidebar.php'; ?>

<section class="pc-container">
    <div class="pc-content">

        <!-- Stats -->
        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8 text-center">
                                <h4><?= $total_docs ?></h4>
                                <p>Total Documents</p>
                            </div>
                            <div class="col-4 text-end">
                                <i class="ti ti-file-text f-28 text-c-yellow"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8 text-center">
                                <h4><?= $verified_docs ?></h4>
                                <p>Verified Documents</p>
                            </div>
                            <div class="col-4 text-end">
                                <i class="ti ti-check f-28 text-c-green"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8 text-center">
                                <h4><?= $pending_docs ?></h4>
                                <p>Pending Documents</p>
                            </div>
                            <div class="col-4 text-end">
                                <i class="ti ti-clock f-28 text-c-blue"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card mt-4">
            <div class="card-header"><strong>Search Documents</strong></div>
            <div class="card-body">
                <form method="GET">
                    <div class="input-group mb-3">
                        <input type="text" name="search" class="form-control" placeholder="Search by verification code..." value="<?= htmlspecialchars($search_query) ?>" required>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>

                <!-- Results Table -->
                <div class="table-responsive">
                    <table class="table table-striped" id="uploads-table">
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
                        <?php if ($result !== null && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['phonenumber']) ?></td>
                                    <td><?= date('M d, Y', strtotime($row['date'])) ?></td>
                                    <td><?= htmlspecialchars($row['Status']) ?></td>
                                    <td><a href="view_doc.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">View</a></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php elseif (!empty($search_query)): ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    No documents found matching '<?= htmlspecialchars($search_query) ?>'.
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    No documents have been sent to this embassy yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Business Verification Requests -->
        <div class="card mt-4">
            <div class="card-header">
                <strong>Business Verification Requests</strong>
                <a href="business_verification.php" class="btn btn-primary btn-sm float-end">
                    <i class="ti ti-plus"></i> Submit New Request
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="businessSearch" class="form-control" placeholder="Search by business name, email, or verification code...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="business-verifications-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Business Name</th>
                                <th>Contact Email</th>
                                <th>Status</th>
                                <th>Verification Code</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($business_verifications)): ?>
                            <?php foreach ($business_verifications as $bv): ?>
                                <tr>
                                    <td><?= $bv['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($bv['business_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($bv['contact_email']) ?></td>
                                    <td>
                                        <?php
                                        $status_class = match($bv['status']) {
                                            'Pending' => 'bg-warning',
                                            'In Progress' => 'bg-info',
                                            'Verified' => 'bg-success',
                                            'Rejected' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $status_class ?>"><?= htmlspecialchars($bv['status']) ?></span>
                                    </td>
                                    <td><code><?= $bv['verification_code'] ?></code></td>
                                    <td><?= date('M d, Y H:i', strtotime($bv['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewBusinessDetails(<?= $bv['id'] ?>)">
                                            <i class="ti ti-eye"></i> View
                                        </button>
                                        <?php if ($bv['status'] === 'Verified' || $bv['status'] === 'Rejected'): ?>
                                            <button class="btn btn-sm btn-secondary" onclick="viewReport(<?= $bv['id'] ?>)">
                                                <i class="ti ti-file-text"></i> Report
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">
                                    No business verification requests found. 
                                    <a href="business_verification.php" class="btn btn-primary btn-sm ms-2">Submit First Request</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>

<footer class="pc-footer text-center py-3">
    <small>DocuPura &copy; <?= date('Y') ?>. All rights reserved.</small>
</footer>

<!-- Scripts -->
<script src="../assets/js/plugins/bootstrap.min.js"></script>
<script src="../assets/js/plugins/jquery.dataTables.min.js"></script>
<script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        if (!$('#uploads-table td[colspan]').length) {
            $('#uploads-table').DataTable();
        }
        
        // Initialize business verifications table
        if (!$('#business-verifications-table td[colspan]').length) {
            $('#business-verifications-table').DataTable({
                order: [[7, 'desc']], // Sort by created date
                pageLength: 10
            });
        }
    });
    
    // Search functionality for business verifications
    $('#businessSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#business-verifications-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    function viewBusinessDetails(id) {
        window.location.href = 'view_business_verification.php?id=' + id;
    }
    
    function viewReport(id) {
        window.location.href = 'view_business_verification.php?id=' + id + '&tab=report';
    }
</script>
</body>
</html>
