<?php $pageTitle = 'Dashboard'; require __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <h1>Dashboard</h1>
    <a href="index.php?page=create_listing" class="btn btn-primary">+ New Listing</a>
</div>

<?php if (isset($_GET['error']) && $_GET['error'] === 'not_verified'): ?>
    <div class="alert alert-error">You must be a verified seller to create listings.</div>
<?php endif; ?>

<?php if ($verReq && $verReq['status'] === 'pending'): ?>
    <div class="alert alert-warning">⏳ Your seller verification request is <strong>pending admin review</strong>. You can browse the panel but cannot create listings yet.</div>
<?php elseif ($verReq && $verReq['status'] === 'rejected'): ?>
    <div class="alert alert-error">❌ Your verification was rejected. Please contact support.</div>
<?php elseif (!empty($seller['seller_verified'])): ?>
    <div class="alert alert-success">✅ You are a verified seller!</div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🟢</div>
        <div class="stat-value"><?= count($active) ?></div>
        <div class="stat-label">Active Listings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-value"><?= count($pending) ?></div>
        <div class="stat-label">Pending Review</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🏁</div>
        <div class="stat-value"><?= count($ended) ?></div>
        <div class="stat-label">Ended Auctions</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⭐</div>
        <div class="stat-value"><?= number_format($seller['reputation_score'] ?? 0, 1) ?></div>
        <div class="stat-label">Reputation Score</div>
    </div>
</div>

<?php if (!empty($active)): ?>
<div class="card mt-4">
    <div class="card-header">🟢 Active Listings</div>
    <table class="table">
        <thead><tr><th>Title</th><th>Current Bid</th><th>Bids</th><th>Ends</th><th>Live Activity</th></tr></thead>
        <tbody>
        <?php foreach ($active as $l): ?>
        <tr>
            <td><?= htmlspecialchars($l['title']) ?></td>
            <td class="bid-amount" id="bid-<?= $l['id'] ?>">৳<?= number_format($l['current_bid'] ?? $l['starting_price'], 2) ?></td>
            <td id="cnt-<?= $l['id'] ?>"><?= $l['bid_count'] ?></td>
            <td><?= date('d M Y H:i', strtotime($l['end_datetime'])) ?></td>
            <td><button class="btn btn-sm btn-outline" onclick="watchBids(<?= $l['id'] ?>)">📡 Watch</button></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Live bid modal -->
<div id="bid-modal" class="modal hidden">
    <div class="modal-box">
        <div class="modal-header">
            <h3>📡 Live Bid Activity</h3>
            <button onclick="closeBidModal()" class="modal-close">✕</button>
        </div>
        <div id="bid-modal-body"><p>Loading...</p></div>
    </div>
</div>

<script>
let bidInterval = null;
let currentListingId = null;

function watchBids(listingId) {
    currentListingId = listingId;
    document.getElementById('bid-modal').classList.remove('hidden');
    fetchBids();
    bidInterval = setInterval(fetchBids, 5000);
}

function fetchBids() {
    fetch('index.php?page=bid_activity&id=' + currentListingId)
        .then(r => r.json())
        .then(data => {
            if (data.error) return;
            // Update dashboard row
            const bidEl = document.getElementById('bid-' + currentListingId);
            const cntEl = document.getElementById('cnt-' + currentListingId);
            if (bidEl) bidEl.textContent = '৳' + parseFloat(data.current_bid || 0).toLocaleString('en', {minimumFractionDigits:2});
            if (cntEl) cntEl.textContent = data.bid_count;

            // Render modal
            let html = `<p><strong>Current Bid:</strong> ৳${parseFloat(data.current_bid||0).toLocaleString('en', {minimumFractionDigits:2})} | <strong>Total Bids:</strong> ${data.bid_count}</p><table class="table"><thead><tr><th>Bidder</th><th>Amount</th><th>Type</th><th>Time</th></tr></thead><tbody>`;
            if (data.bids.length === 0) html += '<tr><td colspan="4">No bids yet</td></tr>';
            data.bids.forEach(b => {
                html += `<tr><td>${b.buyer_name}</td><td>৳${parseFloat(b.amount).toLocaleString('en',{minimumFractionDigits:2})}</td><td>${b.is_auto_bid ? '🤖 Auto' : '👆 Manual'}</td><td>${b.created_at}</td></tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('bid-modal-body').innerHTML = html;
        });
}

function closeBidModal() {
    clearInterval(bidInterval);
    document.getElementById('bid-modal').classList.add('hidden');
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>