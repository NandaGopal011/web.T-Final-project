<?php $pageTitle = 'My Listings'; require __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <h1>My Listings</h1>
    <a href="index.php?page=create_listing" class="btn btn-primary">+ New Listing</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <?php $msgs = ['created'=>'Listing submitted for review!','updated'=>'Listing updated!','cancelled'=>'Listing cancelled.']; ?>
    <div class="alert alert-success"><?= $msgs[$_GET['success']] ?? 'Done!' ?></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <?php $errs = ['has_bids'=>'Cannot edit/cancel — listing already has bids.','cancel_failed'=>'Cancel failed (listing may have bids or already ended).']; ?>
    <div class="alert alert-error"><?= $errs[$_GET['error']] ?? 'Error.' ?></div>
<?php endif; ?>

<!-- Filter tabs -->
<div class="tab-bar">
    <?php foreach (['all'=>'All','pending_review'=>'Pending','active'=>'Active','ended'=>'Ended','cancelled'=>'Cancelled'] as $k => $v): ?>
        <a href="index.php?page=listings&status=<?= $k ?>"
           class="tab <?= (($status??'all') === $k) ? 'tab-active' : '' ?>"><?= $v ?></a>
    <?php endforeach; ?>
</div>

<?php if (empty($listings)): ?>
    <div class="empty-state">No listings found. <a href="index.php?page=create_listing">Create one!</a></div>
<?php else: ?>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Title</th><th>Category</th><th>Condition</th>
                <th>Starting</th><th>Current Bid</th><th>Bids</th>
                <th>Ends</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($listings as $l): ?>
        <tr>
            <td><?= htmlspecialchars($l['title']) ?></td>
            <td><?= htmlspecialchars($l['category_name']) ?></td>
            <td><span class="badge badge-<?= $l['condition'] ?>"><?= ucfirst(str_replace('_',' ',$l['condition'])) ?></span></td>
            <td>৳<?= number_format($l['starting_price'], 2) ?></td>
            <td>৳<?= number_format($l['current_bid'] ?? $l['starting_price'], 2) ?></td>
            <td><?= $l['bid_count'] ?></td>
            <td><?= date('d M Y H:i', strtotime($l['end_datetime'])) ?></td>
            <td><span class="status-badge status-<?= $l['status'] ?>"><?= ucfirst(str_replace('_',' ',$l['status'])) ?></span></td>
            <td class="actions">
                <?php if ($l['status'] === 'active' && $l['bid_count'] == 0): ?>
                    <a href="index.php?page=edit_listing&id=<?= $l['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                    <a href="index.php?page=cancel_listing&id=<?= $l['id'] ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Cancel this listing?')">Cancel</a>
                <?php endif; ?>
                <?php if ($l['status'] === 'ended' && $l['bid_count'] == 0): ?>
                    <a href="index.php?page=relist&id=<?= $l['id'] ?>" class="btn btn-sm btn-outline">Relist</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
