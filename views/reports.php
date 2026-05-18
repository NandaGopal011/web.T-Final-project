<?php
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../controllers/CommissionController.php';

AdminSession::requireAdmin();

$commissionController = new CommissionController();
$period = $_GET['period'] ?? 'month';
$report = $commissionController->getFinancialReport($period);
$topSellers = $commissionController->getTopSellersByRevenue(15);
$topCategories = $commissionController->getTopCategoriesByCommission(10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Reports - Admin</title>
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
                <a href="commission.php" class="nav-link">Commission Rates</a>
                <a href="reports.php" class="nav-link active">Financial Reports</a>
                <a href="analytics.php" class="nav-link">Analytics</a>
                <a href="../public/logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container admin-container">
        <div class="page-header">
            <h1>Financial Reports</h1>
            <p>Platform revenue and commission analysis</p>
        </div>

        <div class="search-box">
            <form method="GET">
                <select name="period" onchange="this.form.submit()">
                    <option value="day" <?php echo $period === 'day' ? 'selected' : ''; ?>>Daily</option>
                    <option value="week" <?php echo $period === 'week' ? 'selected' : ''; ?>>Weekly</option>
                    <option value="month" <?php echo $period === 'month' ? 'selected' : ''; ?>>Monthly</option>
                </select>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value">
                        <?php
                        $totalCommission = 0;
                        foreach ($report as $row) {
                            $totalCommission += $row['total_commission'] ?? 0;
                        }
                        echo '$' . number_format($totalCommission, 2);
                        ?>
                    </div>
                    <div class="stat-label">Total Commission</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value">
                        <?php
                        $totalSales = 0;
                        foreach ($report as $row) {
                            $totalSales += $row['total_sales'] ?? 0;
                        }
                        echo '$' . number_format($totalSales, 2);
                        ?>
                    </div>
                    <div class="stat-label">Total Sales</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-content">
                    <div class="stat-value">
                        <?php
                        $avgRate = $totalSales > 0 ? ($totalCommission / $totalSales) * 100 : 0;
                        echo number_format($avgRate, 2) . '%';
                        ?>
                    </div>
                    <div class="stat-label">Avg Commission Rate</div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="color: #2c3e50; margin-bottom: 15px;">Top Sellers by Revenue</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Seller</th>
                                <th>Total Revenue</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topSellers)): ?>
                                <?php foreach ($topSellers as $seller): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($seller['name']); ?></td>
                                        <td>$<?php echo number_format($seller['total_revenue'], 2); ?></td>
                                        <td><?php echo $seller['total_sales']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center;">No data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="color: #2c3e50; margin-bottom: 15px;">Top Categories by Commission</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Commission</th>
                                <th>Auctions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topCategories)): ?>
                                <?php foreach ($topCategories as $category): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                                        <td>$<?php echo number_format($category['total_commission'], 2); ?></td>
                                        <td><?php echo $category['total_auctions']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center;">No data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #2c3e50; margin-bottom: 15px;">Revenue Trend</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Sales</th>
                            <th>Commission</th>
                            <th>Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($report)): ?>
                            <?php foreach ($report as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['period']); ?></td>
                                    <td>$<?php echo number_format($row['total_sales'] ?? 0, 2); ?></td>
                                    <td>$<?php echo number_format($row['total_commission'] ?? 0, 2); ?></td>
                                    <td>
                                        <?php
                                        $rate = ($row['total_sales'] > 0) ? (($row['total_commission'] ?? 0) / $row['total_sales']) * 100 : 0;
                                        echo number_format($rate, 2) . '%';
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No commission data available</td>
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
