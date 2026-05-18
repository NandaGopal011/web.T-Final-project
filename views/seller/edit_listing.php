<?php $pageTitle = 'Edit Listing'; require __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <h1>Edit Listing</h1>
</div>

<?php if (!empty($_SESSION['listing_errors'])): ?>
    <div class="alert alert-error">
        <?php foreach ($_SESSION['listing_errors'] as $e): ?><div>• <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
        <?php unset($_SESSION['listing_errors']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="index.php?page=update_listing">
        <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">

        <div class="form-row">
            <div class="form-group" style="flex:2">
                <label>Title *</label>
                <input type="text" name="title" required maxlength="200" value="<?= htmlspecialchars($listing['title']) ?>">
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" required>
                    <option value="">— Select —</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $listing['category_id'] == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Description *</label>
            <textarea name="description" rows="5" required><?= htmlspecialchars($listing['description']) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Condition *</label>
                <select name="condition" required>
                    <?php foreach (['new'=>'New','like_new'=>'Like New','good'=>'Good','fair'=>'Fair'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $listing['condition'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Starting Price (৳) *</label>
                <input type="number" name="starting_price" required min="1" step="0.01" value="<?= $listing['starting_price'] ?>">
            </div>
            <div class="form-group">
                <label>Reserve Price (৳)</label>
                <input type="number" name="reserve_price" min="0" step="0.01" value="<?= $listing['reserve_price'] ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Auction End Date & Time *</label>
            <input type="datetime-local" name="end_datetime" required
                   min="<?= date('Y-m-d\TH:i', time() + 3600) ?>"
                   value="<?= date('Y-m-d\TH:i', strtotime($listing['end_datetime'])) ?>">
        </div>

        <div class="form-actions">
            <a href="index.php?page=listings" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Listing</button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
