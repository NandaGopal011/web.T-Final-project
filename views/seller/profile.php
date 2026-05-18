<?php $pageTitle = 'My Profile'; require __DIR__ . '/../partials/header.php'; ?>

<div class="page-header"><h1>My Profile</h1></div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Profile updated successfully!</div>
<?php elseif (isset($_GET['error'])): ?>
    <?php $errs=['wrong_password'=>'Current password is incorrect.','short_password'=>'New password too short (min 6 chars).']; ?>
    <div class="alert alert-error"><?= $errs[$_GET['error']] ?? 'Error.' ?></div>
<?php endif; ?>

<div class="profile-grid">
    <!-- Left: avatar + info -->
    <div class="card profile-card">
        <div class="profile-avatar-wrap">
            <?php if (!empty($seller['profile_pic'])): ?>
                <img src="<?= htmlspecialchars($seller['profile_pic']) ?>" class="profile-avatar-img" alt="Profile">
            <?php else: ?>
                <div class="profile-avatar-placeholder"><?= strtoupper(substr($seller['name'],0,1)) ?></div>
            <?php endif; ?>
        </div>
        <div class="profile-name"><?= htmlspecialchars($seller['name']) ?></div>
        <div class="profile-role"><?= !empty($seller['seller_verified']) ? '✅ Verified Seller' : '⏳ Pending Verification' ?></div>
        <div class="profile-score">⭐ <?= number_format($seller['reputation_score'] ?? 0, 1) ?> reputation</div>
        <?php if (!empty($seller['created_at'])): ?>
        <div class="profile-since">Member since <?= date('M Y', strtotime($seller['created_at'])) ?></div>
        <?php endif; ?>
    </div>

    <!-- Right: edit form -->
    <div class="card" style="flex:2">
        <div class="card-header">Edit Profile</div>
        <form method="POST" action="index.php?page=profile_update" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($seller['name']) ?>">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($seller['phone'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Bio</label>
                <textarea name="bio" rows="3"><?= htmlspecialchars($seller['bio'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Profile Picture</label>
                <input type="file" name="profile_pic" accept="image/*">
            </div>

            <hr>
            <div class="card-header" style="margin: 0 -1.5rem 1rem; padding: 0.75rem 1.5rem;">Change Password <small>(leave blank to keep current)</small></div>
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" minlength="6">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($reviews)): ?>
<div class="card mt-4">
    <div class="card-header">⭐ Reviews Received (<?= count($reviews) ?>)</div>
    <?php foreach (array_slice($reviews, 0, 3) as $r): ?>
    <div class="review-mini">
        <strong><?= htmlspecialchars($r['reviewer_name']) ?></strong>
        <?= str_repeat('⭐', (int)($r['rating'] ?? 0)) ?>
        <span><?= htmlspecialchars(substr($r['review_text'] ?? '', 0, 80)) ?>…</span>
    </div>
    <?php endforeach; ?>
    <?php if (count($reviews) > 3): ?>
        <div style="padding:0.75rem 1.5rem"><a href="index.php?page=reviews">View all <?= count($reviews) ?> reviews →</a></div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>