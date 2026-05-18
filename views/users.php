<?php
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../controllers/UserController.php';

AdminSession::requireAdmin();

$userController = new UserController();
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

if (!empty($search)) {
    $users = $userController->searchUsers($search, $limit, $offset);
} else {
    $users = $userController->getAllUsers($limit, $offset);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? 0;

    if ($action === 'deactivate') {
        $userController->deactivateUser($user_id);
        $_SESSION['success'] = 'User deactivated';
    } elseif ($action === 'reactivate') {
        $userController->reactivateUser($user_id);
        $_SESSION['success'] = 'User reactivated';
    } elseif ($action === 'promote_moderator') {
        $userController->promoteToModerator($user_id);
        $_SESSION['success'] = 'User promoted to moderator';
    } elseif ($action === 'demote_moderator') {
        $userController->demoteFromModerator($user_id);
        $_SESSION['success'] = 'User demoted to buyer';
    }

    header('Location: users.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="admin-page">
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">Auction Platform Admin</div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="verifications.php" class="nav-link">Seller Verifications</a>
                <a href="users.php" class="nav-link active">Manage Users</a>
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
            <h1>Manage User Accounts</h1>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="search-box">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <?php if (empty($users)): ?>
            <div class="alert alert-info">No users found</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Reputation</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $user['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($user['reputation_score'], 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <form method="POST" action="" class="inline-form">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <?php if ($user['is_active']): ?>
                                            <button type="submit" name="action" value="deactivate" class="btn btn-sm btn-danger">Deactivate</button>
                                        <?php else: ?>
                                            <button type="submit" name="action" value="reactivate" class="btn btn-sm btn-success">Reactivate</button>
                                        <?php endif; ?>

                                        <?php if ($user['role'] === 'buyer' || $user['role'] === 'seller'): ?>
                                            <button type="submit" name="action" value="promote_moderator" class="btn btn-sm btn-info"> Moderator</button>
                                        <?php elseif ($user['role'] === 'moderator'): ?>
                                            <button type="submit" name="action" value="demote_moderator" class="btn btn-sm btn-warning"> Buyer</button>
                                        <?php endif; ?>
                                    </form>
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
