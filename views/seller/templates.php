<?php $pageTitle = 'Templates'; require __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <h1>Auction Templates</h1>
    <button class="btn btn-primary" onclick="document.getElementById('new-template-form').classList.toggle('hidden')">+ New Template</button>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= $_GET['success'] === 'saved' ? 'Template saved!' : 'Template deleted.' ?></div>
<?php endif; ?>

<!-- Inline create template form -->
<div id="new-template-form" class="card hidden mb-3">
    <div class="card-header">New Template</div>
    <form method="POST" action="index.php?page=save_template">
        <?php
        // need categories - reload
        $db = getDB();
        $cats = $db->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
        ?>
        <div class="form-row">
            <div class="form-group" style="flex:2">
                <label>Title *</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id">
                    <option value="">—</option>
                    <?php foreach ($cats as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Condition</label>
                <select name="condition">
                    <option value="new">New</option><option value="like_new">Like New</option>
                    <option value="good">Good</option><option value="fair">Fair</option>
                </select>
            </div>
            <div class="form-group">
                <label>Starting Price (৳)</label>
                <input type="number" name="starting_price" min="0" step="0.01">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Template</button>
        </div>
    </form>
</div>

<?php if (empty($templates)): ?>
    <div class="empty-state">No templates yet. Create one to speed up future listings!</div>
<?php else: ?>
<div class="templates-grid">
    <?php foreach ($templates as $t): ?>
    <div class="template-card">
        <div class="template-title"><?= htmlspecialchars($t['title']) ?></div>
        <div class="template-meta"><?= htmlspecialchars($t['category_name'] ?? '—') ?> · <?= ucfirst(str_replace('_',' ',$t['condition'] ?? '')) ?> · ৳<?= number_format($t['starting_price'] ?? 0, 2) ?></div>
        <p class="template-desc"><?= nl2br(htmlspecialchars(substr($t['description'] ?? '', 0, 100))) ?><?= strlen($t['description'] ?? '') > 100 ? '…' : '' ?></p>
        <div class="template-actions">
            <a href="index.php?page=listing_from_template&id=<?= $t['id'] ?>" class="btn btn-sm btn-primary">Use</a>
            <a href="index.php?page=delete_template&id=<?= $t['id'] ?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('Delete this template?')">Delete</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
