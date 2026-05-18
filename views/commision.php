<?php
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../controllers/CommissionController.php';

AdminSession::requireAdmin();

$commissionController = new CommissionController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'set_default') {
        $rate = floatval($_POST['default_rate'] ?? 0);
        if ($rate > 0 && $rate <= 100) {
            $commissionController->setDefaultCommissionRate($rate);
            $_SESSION['success'] = 'Default commission rate updated';
        } else {
            $_SESSION['error'] = 'Invalid commission rate';
        }
    } elseif ($action === 'set_seller') {
        $seller_id = intval($_POST['seller_id'] ?? 0);
        $rate = floatval($_POST['seller_rate'] ?? 0);
        if ($seller_id > 0 && $rate > 0 && $rate <= 100) {
            $commissionController->setSellerCommissionRate($seller_id, $rate);
            $_SESSION['success'] = 'Seller commission rate updated';
        } else {
            $_SESSION['error'] = 'Invalid seller or rate';
        }
    }

    header('Location: commission.php');
    exit;
}

$rates = $commissionController->getFinancialReport('month');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commission Rates - Admin</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="admin-page">
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">Auction Platform Admin</div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="verifications.php" class="nav-link">Seller Verifications</a>
                <a href="users.php" class="nav-link">Manage Users</a>
                <a href="listings.php" class="nav-link">Manage Listings</a>
                <a href="commission.php" class="nav-link active">Commission Rates</a>
                <a href="reports.php" class="nav-link">Financial Reports</a>
                <a href="analytics.php" class="nav-link">Analytics</a>
                <a href="../public/logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container admin-container">
        <div class="page-header">
            <h1>Commission Rate Management</h1>
            <p>Set default and per-seller commission rates</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #2c3e50; margin-bottom: 15px;">Set Default Commission Rate</h2>
            <form method="POST" action="" style="display: flex; gap: 10px; align-items: flex-end;">
                <div class="form-group" style="flex: 1;">
                    <label for="default_rate">Commission Rate (%)</label>
                    <input type="number" id="default_rate" name="default_rate" min="0" max="100" step="0.01" required placeholder="e.g., 5.5">
                </div>
                <button type="submit" name="action" value="set_default" class="btn btn-primary">Update</button>
            </form>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #2c3e50; margin-bottom: 15px;">Set Per-Seller Rate</h2>
            <form method="POST" action="" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: flex-end;">
                <div class="form-group">
                    <label for="seller_id">Seller ID</label>
                    <input type="number" id="seller_id" name="seller_id" min="1" required placeholder="Enter seller ID">
                </div>
                <div class="form-group">
                    <label for="seller_rate">Commission Rate (%)</label>
                    <input type="number" id="seller_rate" name="seller_rate" min="0" max="100" step="0.01" required placeholder="e.g., 3.5">
                </div>
                <button type="submit" name="action" value="set_seller" class="btn btn-primary">Set</button>
            </form>
        </div>

        <div style="margin-top: 30px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #2c3e50; margin-bottom: 15px;">Commission Trends</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Total Sales</th>
                            <th>Total Commission</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rates)): ?>
                            <?php foreach ($rates as $rate): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rate['period']); ?></td>
                                    <td>$<?php echo number_format($rate['total_sales'] ?? 0, 2); ?></td>
                                    <td>$<?php echo number_format($rate['total_commission'] ?? 0, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">No commission data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../public/js/main.js"></script>
</body>
</html>
