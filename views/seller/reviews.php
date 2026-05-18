<?php $pageTitle = 'My Reviews'; require __DIR__ . '/../partials/header.php'; ?>

<div class="page-header"><h1>My Reviews</h1></div>

<?php if (empty($reviews)): ?>
    <div class="empty-state">No reviews yet. Complete sales to receive reviews from buyers.</div>
<?php else: ?>
<?php
$avg = count($reviews) ? array_sum(array_column($reviews, 'rating')) / count($reviews) : 0;
?>
<div class="card mb-3">
    <div class="card-body text-center">
        <div class="big-stat"><?= number_format($avg, 1) ?> ⭐</div>
        <div>Average rating from <?= count($reviews) ?> review(s)</div>
    </div>
</div>

<div class="reviews-list">
<?php foreach ($reviews as $r): ?>
<div class="review-card">
    <div class="review-header">
        <div class="review-avatar"><?= strtoupper(substr($r['reviewer_name'], 0, 1)) ?></div>
        <div>
            <div class="review-name"><?= htmlspecialchars($r['reviewer_name']) ?></div>
            <div class="review-listing">re: <?= htmlspecialchars($r['listing_title']) ?></div>
        </div>
        <div class="review-rating">
            <?= str_repeat('⭐', (int)$r['rating']) ?>
            <span><?= $r['rating'] ?>/5</span>
        </div>
    </div>
    <?php if ($r['review_text']): ?>
        <p class="review-text"><?= nl2br(htmlspecialchars($r['review_text'])) ?></p>
    <?php endif; ?>
    <div class="review-date"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
