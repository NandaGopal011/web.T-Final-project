<?php
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../controllers/ListingController.php';
require_once __DIR__ . '/../controllers/FeaturedController.php';

AdminSession::requireAdmin();

$listingController = new ListingController();
$featuredController = new FeaturedController();
$status = $_GET['status'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$listings = $listingController->getAllListings($status ?: null, null, $limit, $offset);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $listing_id = $_POST['listing_id'] ?? 0;

    if ($action === 'cancel') {
        $reason = $_POST['reason'] ?? '';
        $listingController->cancelListing($listing_id, $reason);
        $_SESSION['success'] = 'Listing cancelled successfully';
    } elseif ($action === 'feature') {
        $featuredController->addFeaturedListing($listing_id);
        $_SESSION['success'] = 'Listing featured successfully';
    } elseif ($action === 'unfeature') {
        $featuredController->removeFeaturedListing($listing_id);
        $_SESSION['success'] = 'Listing removed from featured';
    }

    header('Location: listings.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Listings - Admin</title>
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
                <a href="listings.php" class="nav-link active">Manage Listings</a>
                <a href="commission.php" class="nav-link">Commission Rates</a>
                <a href="reports.php" class="nav-link">Financial Reports</a>
                <a href="analytics.php" class="nav-link">Analytics</a>
                <a href="../public/logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container admin-container">
        <div class="page-header">
            <h1>Manage Listings</h1>
            <p>Review and manage all platform listings</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="search-box">
            <form method="GET" class="search-form">
                <select name="status" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending_review" <?php echo $status === 'pending_review' ? 'selected' : ''; ?>>Pending Review</option>
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="ended" <?php echo $status === 'ended' ? 'selected' : ''; ?>>Ended</option>
                    <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </form>
        </div>

        <?php if (empty($listings)): ?>
            <div class="alert alert-info">No listings found</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Seller</th>
                            <th>Category</th>
                            <th>Current Bid</th>
                            <th>Status</th>
                            <th>Ends</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listings as $listing): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($listing['title']); ?></td>
                                <td><?php echo htmlspecialchars($listing['seller_name']); ?></td>
                                <td><?php echo htmlspecialchars($listing['category_name']); ?></td>
                                <td>$<?php echo number_format($listing['current_bid'] ?? $listing['starting_price'], 2); ?></td>
                                <td>
                                    <span class="badge">
                                        <?php echo ucfirst(str_replace('_', ' ', $listing['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, H:i', strtotime($listing['end_datetime'])); ?></td>
                                <td>
                                    <?php if ($listing['status'] === 'active'): ?>
                                        <form method="POST" action="" class="inline-form" style="display: inline;">
                                            <input type="hidden" name="listing_id" value="<?php echo $listing['id']; ?>">
                                            <button type="submit" name="action" value="feature" class="btn btn-sm btn-info">Feature</button>
                                            <button type="submit" name="action" value="cancel" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this listing?')">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="../public/js/main.js"></script>
</body>
</html>
