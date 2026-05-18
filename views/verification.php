<?php
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../controllers/SellerController.php';

AdminSession::requireAdmin();

$sellerController = new SellerController();
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$verifications = $sellerController->getPendingVerifications($limit, $offset);

// Get total count - instantiate model directly for this
require_once __DIR__ . '/../models/SellerModel.php';
$sellerModel = new SellerModel();
$total_count = $sellerModel->getPendingVerificationsCount();
$total_pages = ceil($total_count / $limit);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $verification_id = $_POST['verification_id'] ?? 0;

    if ($action === 'approve') {
        $sellerController->approveVerification($verification_id);
        $_SESSION['success'] = 'Seller verification approved';
    } elseif ($action === 'reject') {
        $sellerController->rejectVerification($verification_id);
        $_SESSION['success'] = 'Seller verification rejected';
    }

    header('Location: verifications.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Verifications - Admin</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="admin-page">
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">Auction Platform Admin</div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="verifications.php" class="nav-link active">Seller Verifications</a>
                <a href="users.php" class="nav-link">Manage Users</a>
                <a href="listings.php" class="nav-link">Manage Listings</a>
                <a href="commission.php" class="nav-link">Commission Rates</a>
                <a href="reports.php" class="nav-link">Financial Reports</a>
                <a href="analytics.php" class="nav-link">Analytics</a>
                <a href="../public/logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container admin-container">
        <div class="page-header">
            <h1>Seller Verification Requests</h1>
            <p>Review and approve/reject seller applications</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($verifications)): ?>
            <div class="alert alert-info">No pending seller verification requests</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Motivation</th>
                            <th>Submitted</th>
                            <th>Document</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($verifications as $verification): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($verification['name']); ?></td>
                                <td><?php echo htmlspecialchars($verification['email']); ?></td>
                                <td><?php echo htmlspecialchars($verification['phone'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars(substr($verification['motivation'], 0, 50)) . '...'; ?></td>
                                <td><?php echo date('M d, Y', strtotime($verification['submitted_at'])); ?></td>
                                <td>
                                    <?php if ($verification['id_document_path']): ?>
                                        <a href="<?php echo htmlspecialchars($verification['id_document_path']); ?>" target="_blank" class="btn btn-sm btn-info">View</a>
                                    <?php else: ?>
                                        <span class="badge">None</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="verification_id" value="<?php echo $verification['id']; ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="pagination-link <?php echo $page == $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="../public/js/main.js"></script>
</body>
</html>
