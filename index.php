<?php
require_once __DIR__ . '/config/Session.php';
require_once __DIR__ . '/models/AuthModel.php';

AdminSession::start();

// If already logged in, go to dashboard
if (AdminSession::check()) {
    header('Location: views/dashboard.php');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else {
        $authModel = new AuthModel();
        $user_data = $authModel->login($email, $password);

        if ($user_data && $user_data['role'] === 'admin') {
            AdminSession::create(
                $user_data['id'],
                $user_data['name'],
                $user_data['email'],
                $user_data['role']
            );
            header('Location: views/dashboard.php');
            exit;
        } else {
            $error = 'Invalid credentials or insufficient permissions';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Auction Platform</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>Admin Portal</h1>
            <p>Auction Platform Management</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="admin@auction.local" autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter password">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="login-footer">
                <p>Demo Credentials:</p>
                <small>Email: admin@auction.local</small><br>
                <small>Password: admin123</small><br>
            </div>
        </div>
    </div>
</body>
</html>
