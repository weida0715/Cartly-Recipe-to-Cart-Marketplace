<?php use App\Helpers\Csrf; ?>
<?php
$counts = $counts ?? ['pending' => 0, 'reviewed' => 0, 'resolved' => 0, 'total' => 0];
?>
<h2>Content Moderation</h2>
<div class="stat-grid">
  <div class="stat"><div class="num"><?= (int) $counts['total'] ?></div><div class="label">Total reports</div></div>
  <div class="stat"><div class="num"><?= (int) $counts['pending'] ?></div><div class="label">Pending reports</div></div>
  <div class="stat"><div class="num"><?= (int) $counts['reviewed'] ?></div><div class="label">Reviewed reports</div></div>
  <div class="stat"><div class="num"><?= (int) $counts['resolved'] ?></div><div class="label">Resolved reports</div></div>
</div>

<section class="card chart-card">
  <h3>Report status breakdown</h3>
  <div class="d3-chart" data-chart="pie" data-chart-values='<?= htmlspecialchars(json_encode([
    ['label' => 'Pending', 'value' => (int) $counts['pending']],
    ['label' => 'Reviewed', 'value' => (int) $counts['reviewed']],
    ['label' => 'Resolved', 'value' => (int) $counts['resolved']],
  ]), ENT_QUOTES) ?>'></div>
</section>

<?php if (!$reports): ?>
  <div class="card text-muted">No reports found.</div>
<?php else:
  foreach ($reports as $r): ?>
    <div class="card">
      <div class="flex-between">
        <strong>Report #<?= (int) $r['report_id'] ?> - <?= htmlspecialchars($r['target_type']) ?>
          #<?= (int) $r['target_id'] ?></strong>
        <span class="text-muted"><?= htmlspecialchars($r['created_at']) ?> by <?= htmlspecialchars($r['reporter']) ?></span>
      </div>
      <p><strong>Status:</strong> <span class="badge"><?= htmlspecialchars($r['status']) ?></span></p>
      <p><strong>Reason:</strong> <?= nl2br(htmlspecialchars($r['reason'] ?? '')) ?></p>

      <?php if ($r['target_type'] === 'product'): ?>
        <h4>Reported product</h4>
        <?php if (!empty($r['product_name'])): ?>
          <table class="table">
            <tbody>
              <tr><th>Name</th><td><?= htmlspecialchars($r['product_name']) ?></td></tr>
              <tr><th>Store</th><td><?= htmlspecialchars($r['product_store_name'] ?? 'Unknown') ?></td></tr>
              <tr><th>Category</th><td><?= htmlspecialchars($r['product_category_name'] ?? 'Uncategorized') ?></td></tr>
              <tr><th>Ingredient</th><td><?= htmlspecialchars($r['product_ingredient_name'] ?? 'Not linked') ?></td></tr>
              <tr><th>Price</th><td>RM <?= number_format((float) $r['product_price'], 2) ?></td></tr>
              <tr><th>Stock</th><td><?= (int) $r['product_stock_quantity'] ?> <?= htmlspecialchars($r['product_package_unit'] ?? '') ?></td></tr>
              <tr><th>Status</th><td><?= htmlspecialchars($r['product_status']) ?></td></tr>
              <tr><th>Description</th><td><?= nl2br(htmlspecialchars($r['product_description'] ?? '')) ?></td></tr>
            </tbody>
          </table>
        <?php else: ?>
          <p class="text-muted">The reported product could not be found.</p>
        <?php endif; ?>
      <?php elseif ($r['target_type'] === 'recipe'): ?>
        <h4>Reported recipe</h4>
        <?php if (!empty($r['recipe_title'])): ?>
          <p>
            <strong><?= htmlspecialchars($r['recipe_title']) ?></strong>
            <span class="text-muted">by <?= htmlspecialchars($r['recipe_author'] ?? 'Unknown') ?></span>
          </p>
          <p>
            Cuisine: <?= htmlspecialchars($r['recipe_cuisine_type'] ?? 'Not set') ?> |
            Difficulty: <?= htmlspecialchars($r['recipe_difficulty'] ?? 'Not set') ?> |
            Time: <?= (int) $r['recipe_prep_time'] + (int) $r['recipe_cook_time'] ?> mins |
            Status: <?= htmlspecialchars($r['recipe_status']) ?>
          </p>
        <?php else: ?>
          <p class="text-muted">The reported recipe could not be found.</p>
        <?php endif; ?>
      <?php elseif ($r['target_type'] === 'review'): ?>
        <h4>Reported review</h4>
        <?php if (!empty($r['review_author'])): ?>
          <p>
            <strong><?= htmlspecialchars($r['review_author']) ?></strong>
            rated <?= (int) $r['review_rating'] ?>/5
            on <?= htmlspecialchars($r['review_product_name'] ?? $r['review_recipe_title'] ?? 'Unknown target') ?>
          </p>
          <p><strong>Review status:</strong> <?= htmlspecialchars($r['review_status']) ?></p>
          <p><?= nl2br(htmlspecialchars($r['review_comment'] ?? '')) ?></p>
        <?php else: ?>
          <p class="text-muted">The reported review could not be found.</p>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ($r['status'] !== 'resolved'): ?>
        <div class="flex">
          <form method="post" action="<?= BASE_URL ?>/admin/reports/<?= (int) $r['report_id'] ?>/resolve">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="resolve">
            <button class="btn btn-primary">Resolve</button>
          </form>
          <form method="post" action="<?= BASE_URL ?>/admin/reports/<?= (int) $r['report_id'] ?>/resolve">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="reviewed">
            <button class="btn btn-outline">Mark reviewed</button>
          </form>
          <form method="post" action="<?= BASE_URL ?>/admin/reports/<?= (int) $r['report_id'] ?>/resolve">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="hide">
            <button class="btn btn-danger">Hide/remove target</button>
          </form>
        </div>
      <?php elseif (!empty($r['resolved_at'])): ?>
        <p class="text-muted">Resolved at <?= htmlspecialchars($r['resolved_at']) ?></p>
      <?php endif; ?>
    </div>
  <?php endforeach; endif; ?>
