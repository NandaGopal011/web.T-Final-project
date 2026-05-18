<?php $pageTitle = 'Ended Auctions'; require __DIR__ . '/../partials/header.php'; ?>

<div class="page-header"><h1>Ended Auctions</h1></div>

<?php if (empty($listings)): ?>
    <div class="empty-state">No ended auctions yet.</div>
<?php else: ?>
<div class="card">
    <table class="table">
        <thead>
            <tr><th>Title</th><th>Final Bid</th><th>Reserve</th><th>Result</th><th>Winner</th><th>Contact</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($listings as $l):
            $winner = $winners[$l['id']] ?? null;
            $reserveMet = !$l['reserve_price'] || ($l['current_bid'] >= $l['reserve_price']);
        ?>
        <tr>
            <td><?= htmlspecialchars($l['title']) ?></td>
            <td>৳<?= number_format($l['current_bid'] ?? 0, 2) ?></td>
            <td><?= $l['reserve_price'] ? '৳' . number_format($l['reserve_price'], 2) : '—' ?></td>
            <td>
                <?php if ($winner && $reserveMet): ?>
                    <span class="status-badge status-active">✅ Sold</span>
                <?php elseif ($winner && !$reserveMet): ?>
                    <span class="status-badge status-cancelled">⚠️ Reserve Not Met</span>
                <?php else: ?>
                    <span class="status-badge status-cancelled">❌ Unsold</span>
                <?php endif; ?>
            </td>
            <td><?= $winner ? htmlspecialchars($winner['name']) : '—' ?></td>
            <td>
                <?php if ($winner && $reserveMet): ?>
                    <span class="contact-info">📧 <?= htmlspecialchars($winner['email']) ?><?= $winner['phone'] ? ' | 📞 ' . htmlspecialchars($winner['phone']) : '' ?></span>
                <?php else: ?>—<?php endif; ?>
            </td>
            <td>
                <?php if (!$winner || !$reserveMet): ?>
                    <a href="index.php?page=relist&id=<?= $l['id'] ?>" class="btn btn-sm btn-outline">🔄 Relist</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
