<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Login — AuctionHub</title>
<link rel="stylesheet" href="public/css/seller.css">
</head>
<body class="auth-body">
<div class="auth-card">
    <div class="auth-brand">🏷️ AuctionHub<span>Seller Portal</span></div>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
        <div class="alert alert-success">Registration submitted! Login once your account is verified.</div>
    <?php endif; ?>

    <?php
    $msgs = [
        'fill_all'      => 'Please fill in all fields.',
        'invalid'       => 'Invalid email or password.',
        'not_seller'    => 'This portal is for sellers only.',
        'access_denied' => 'Access denied.',
    ];
    if (isset($_GET['error']) && isset($msgs[$_GET['error']])): ?>
        <div class="alert alert-error"><?= $msgs[$_GET['error']] ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?page=login_submit">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required autocomplete="email" placeholder="seller@example.com">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
        </div>
        <button class="btn btn-primary btn-block" type="submit">Login</button>
    </form>
    <p class="auth-link">New seller? <a href="index.php?page=register">Register here</a></p>
</div>
</body>
</html>
