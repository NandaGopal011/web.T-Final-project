<?php $pageTitle = 'Analytics'; require __DIR__ . '/../partials/header.php'; ?>

<div class="page-header"><h1>Seller Analytics</h1></div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📋</div>
        <div class="stat-value"><?= $data['total'] ?></div>
        <div class="stat-label">Total Listings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🏆</div>
        <div class="stat-value"><?= $data['won'] ?></div>
        <div class="stat-label">Auctions Won (Sold)</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-value">৳<?= number_format($data['avgPrice'] ?? 0, 2) ?></div>
        <div class="stat-label">Avg. Sale Price</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💵</div>
        <div class="stat-value">৳<?= number_format($data['revenue'] ?? 0, 2) ?></div>
        <div class="stat-label">Total Revenue</div>
    </div>
</div>

<div class="analytics-row mt-4">
    <div class="card" style="flex:1">
        <div class="card-header">🏷️ Most Popular Category</div>
        <div class="card-body text-center">
            <?php if ($data['popularCat']): ?>
                <div class="big-stat"><?= htmlspecialchars($data['popularCat']['name']) ?></div>
                <div><?= $data['popularCat']['cnt'] ?> listing(s)</div>
            <?php else: ?>
                <div>No data yet</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card" style="flex:2">
        <div class="card-header">📈 Sales Trend (Last 6 Months)</div>
        <div class="card-body">
            <?php if (empty($data['trend'])): ?>
                <div class="empty-state">No sales data yet.</div>
            <?php else: ?>
                <div class="bar-chart">
                <?php
                $maxCount = max(array_column($data['trend'], 'count')) ?: 1;
                foreach ($data['trend'] as $t):
                    $h = round(($t['count'] / $maxCount) * 120);
                ?>
                    <div class="bar-item">
                        <div class="bar-fill" style="height:<?= $h ?>px" title="<?= $t['count'] ?> sold"></div>
                        <div class="bar-label"><?= $t['month'] ?></div>
                        <div class="bar-val"><?= $t['count'] ?></div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
