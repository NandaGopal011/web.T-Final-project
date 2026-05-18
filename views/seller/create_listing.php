<?php $pageTitle = isset($prefill) ? 'Relist / New from Template' : 'Create Listing';
require __DIR__ . '/../partials/header.php'; ?>

<div class="page-header">
    <h1><?= $pageTitle ?></h1>
</div>

<?php if (!empty($_SESSION['listing_errors'])): ?>
    <div class="alert alert-error">
        <?php foreach ($_SESSION['listing_errors'] as $e): ?>
            <div>• <?= htmlspecialchars($e) ?></div>
        <?php endforeach; unset($_SESSION['listing_errors']); ?>
    </div>
<?php endif; ?>

<?php $old = $_SESSION['listing_old'] ?? $prefill ?? []; unset($_SESSION['listing_old']); ?>

<!-- Quick-fill from template -->
<?php if (!empty($templates)): ?>
<div class="card mb-3">
    <div class="card-header">📁 Quick-fill from Template</div>
    <div class="card-body">
        <select id="template-select" class="form-control" onchange="fillFromTemplate(this)">
            <option value="">— Select a template —</option>
            <?php foreach ($templates as $t): ?>
                <option value="<?= $t['id'] ?>"
                    data-title="<?= htmlspecialchars($t['title']) ?>"
                    data-description="<?= htmlspecialchars($t['description']) ?>"
                    data-category="<?= $t['category_id'] ?>"
                    data-condition="<?= $t['condition'] ?>"
                    data-price="<?= $t['starting_price'] ?>">
                    <?= htmlspecialchars($t['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="index.php?page=store_listing" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group" style="flex:2">
                <label>Title *</label>
                <input type="text" name="title" id="f-title" required maxlength="200"
                       value="<?= htmlspecialchars($old['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" id="f-category" required>
                    <option value="">— Select —</option>
                    <?php
                    $parents = array_filter($categories, fn($c) => !$c['parent_id']);
                    $children = array_filter($categories, fn($c) => $c['parent_id']);
                    foreach ($parents as $p):
                        $subs = array_filter($children, fn($c) => $c['parent_id'] == $p['id']);
                    ?>
                        <?php if ($subs): ?>
                            <optgroup label="<?= htmlspecialchars($p['name']) ?>">
                                <?php foreach ($subs as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= ($old['category_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php else: ?>
                            <option value="<?= $p['id'] ?>" <?= ($old['category_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Description *</label>
            <textarea name="description" id="f-description" rows="5" required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Condition *</label>
                <select name="condition" id="f-condition" required>
                    <?php foreach (['new'=>'New','like_new'=>'Like New','good'=>'Good','fair'=>'Fair'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= ($old['condition'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Starting Price (৳) *</label>
                <input type="number" name="starting_price" id="f-price" required min="1" step="0.01"
                       value="<?= htmlspecialchars($old['starting_price'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Reserve Price (৳) <small>optional</small></label>
                <input type="number" name="reserve_price" min="0" step="0.01"
                       value="<?= htmlspecialchars($old['reserve_price'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Auction End Date & Time * <small>(min. 1 hour from now)</small></label>
            <input type="datetime-local" name="end_datetime" required
                   min="<?= date('Y-m-d\TH:i', time() + 3600) ?>"
                   value="<?= htmlspecialchars($old['end_datetime'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Images (up to 5 — JPG, PNG, WEBP)</label>
            <input type="file" name="images[]" accept="image/*" multiple>
        </div>

        <div class="form-check">
            <label>
                <input type="checkbox" name="save_as_template" value="1">
                Save as reusable template
            </label>
        </div>

        <div class="form-actions">
            <a href="index.php?page=listings" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Submit Listing</button>
        </div>
    </form>
</div>

<script>
function fillFromTemplate(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) return;
    document.getElementById('f-title').value       = opt.dataset.title;
    document.getElementById('f-description').value = opt.dataset.description;
    document.getElementById('f-category').value    = opt.dataset.category;
    document.getElementById('f-condition').value   = opt.dataset.condition;
    document.getElementById('f-price').value       = opt.dataset.price;
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
