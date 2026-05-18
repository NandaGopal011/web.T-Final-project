<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Registration — AuctionHub</title>
<link rel="stylesheet" href="public/css/seller.css">
</head>
<body class="auth-body">
<div class="auth-card auth-card-wide">
    <div class="auth-brand">🏷️ AuctionHub<span>Seller Registration</span></div>

    <?php if (!empty($_SESSION['reg_errors'])): ?>
        <div class="alert alert-error">
            <?php foreach ($_SESSION['reg_errors'] as $e): ?>
                <div>• <?= htmlspecialchars($e) ?></div>
            <?php endforeach; unset($_SESSION['reg_errors']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?page=register_submit" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($_SESSION['listing_old']['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone">
            </div>
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <div class="form-group">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_password" required>
            </div>
        </div>
        <div class="form-group">
            <label>Bio</label>
            <textarea name="bio" rows="2" placeholder="Tell buyers about yourself..."></textarea>
        </div>
        <div class="form-group">
            <label>Seller Motivation Statement *</label>
            <textarea name="motivation" rows="3" required placeholder="Why do you want to sell on AuctionHub?"></textarea>
        </div>
        <div class="form-group">
            <label>ID Document * (JPG, PNG, or PDF)</label>
            <input type="file" name="id_document" accept=".jpg,.jpeg,.png,.pdf" required>
            <small>Your ID document will be reviewed by an admin before approval.</small>
        </div>
        <button class="btn btn-primary btn-block" type="submit">Submit Registration</button>
    </form>
    <p class="auth-link">Already registered? <a href="index.php?page=login">Login</a></p>
</div>
</body>
</html>
