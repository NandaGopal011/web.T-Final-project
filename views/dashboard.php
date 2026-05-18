<?php
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../controllers/AnalyticsController.php';

AdminSession::requireAdmin();

$analyticsController = new AnalyticsController();
$stats = $analyticsController->getDashboardStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Auction Platform</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="admin-page">
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">Auction Platform Admin</div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <a href="verifications.php" class="nav-link">Seller Verifications</a>
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
            <h1>Admin Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value" data-stat-key="users_buyer"><?php echo $stats['users_buyer']; ?></div>
                    <div class="stat-label">Total Buyers</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value" data-stat-key="users_seller"><?php echo $stats['users_seller']; ?></div>
                    <div class="stat-label">Total Sellers</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"></div>   
                <div class="stat-content">
                    <div class="stat-value" data-stat-key="users_moderator"><?php echo $stats['users_moderator']; ?></div>
                    <div class="stat-label">Moderators</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value" data-stat-key="active_listings"><?php echo $stats['active_listings']; ?></div>
                    <div class="stat-label">Active Listings</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value" data-stat-key="commission_this_month">$<?php echo number_format($stats['commission_this_month'], 2); ?></div>
                    <div class="stat-label">Commission (This Month)</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value" data-stat-key="bids_today"><?php echo $stats['bids_today']; ?></div>
                    <div class="stat-label">Bids Today</div>
                </div>
            </div>

            <div class="stat-card alert-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value" data-stat-key="pending_verifications"><?php echo $stats['pending_verifications']; ?></div>
                    <div class="stat-label">Pending Verifications</div>
                </div>
            </div>
        </div>

        <div class="dashboard-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="verifications.php" class="btn btn-primary">Review Seller Verifications</a>
                <a href="users.php" class="btn btn-secondary">Manage Users</a>
                <a href="listings.php" class="btn btn-secondary">Review Listings</a>
                <a href="commission.php" class="btn btn-secondary">Set Commission Rates</a>
                <a href="announcements.php" class="btn btn-secondary">Post Announcement</a>
            </div>
        </div>
    </div>

    <script src="../public/js/main.js"></script>
</body>
</html>
