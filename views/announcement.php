<?php
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';

AdminSession::requireAdmin();

$announcementController = new AnnouncementController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (!empty($title) && !empty($content)) {
            $announcementController->createAnnouncement($title, $content);
            $_SESSION['success'] = 'Announcement posted successfully';
        } else {
            $_SESSION['error'] = 'Title and content are required';
        }
    } elseif ($action === 'deactivate') {
        $announcement_id = intval($_POST['announcement_id'] ?? 0);
        if ($announcement_id > 0) {
            $announcementController->deactivateAnnouncement($announcement_id);
            $_SESSION['success'] = 'Announcement removed';
        }
    }

    header('Location: announcements.php');
    exit;
}

$announcements = $announcementController->getAnnouncements();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Admin</title>
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
                <a href="reports.php" class="nav-link">Financial Reports</a>
                <a href="analytics.php" class="nav-link">Analytics</a>
                <a href="../public/logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container admin-container">
        <div class="page-header">
            <h1>Platform Announcements</h1>
            <p>Post messages visible to all users on login</p>
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
            <h2 style="color: #2c3e50; margin-bottom: 15px;">Create New Announcement</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="title">Announcement Title</label>
                    <input type="text" id="title" name="title" required placeholder="e.g., Scheduled Maintenance">
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="6" required placeholder="Enter announcement text..."></textarea>
                </div>

                <button type="submit" name="action" value="create" class="btn btn-primary">Post Announcement</button>
            </form>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #2c3e50; margin-bottom: 15px;">Active Announcements</h2>

            <?php if (empty($announcements)): ?>
                <div class="alert alert-info">No active announcements</div>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div style="background: #f9f9f9; padding: 15px; margin-bottom: 15px; border-radius: 4px; border-left: 4px solid #3498db;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 5px 0; color: #2c3e50;">
                                    <?php echo htmlspecialchars($announcement['title']); ?>
                                </h3>
                                <p style="margin: 0 0 10px 0; color: #999; font-size: 12px;">
                                    Posted by <?php echo htmlspecialchars($announcement['admin_name']); ?> on
                                    <?php echo date('M d, Y H:i', strtotime($announcement['created_at'])); ?>
                                </p>
                                <p style="margin: 0; color: #333;">
                                    <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                                </p>
                            </div>
                            <form method="POST" style="margin-left: 10px;">
                                <input type="hidden" name="announcement_id" value="<?php echo $announcement['id']; ?>">
                                <button type="submit" name="action" value="deactivate" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <style>
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }

        textarea:focus {
            outline: none;
            border-color: #3498db;
        }
    </style>

    <script src="../public/js/main.js"></script>
</body>
</html>
