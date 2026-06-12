<?php use App\Helpers\Csrf; ?>
<h2>Cart preview <?php if ($recipe): ?><span class="text-muted">— from <?= htmlspecialchars($recipe['recipe_title']) ?> (×<?= (int)$servings ?>)</span><?php endif; ?></h2>

<?php foreach ($warnings as $w): ?>
  <div class="preview-warning">⚠️ <?= htmlspecialchars($w) ?></div>
<?php endforeach; ?>

<?php if (!$grouped): ?>
  <div class="card text-center text-muted">No items could be generated. Adjust the recipe or stock.</div>
<?php else: ?>
  <?php $grand = 0; foreach ($grouped as $sid => $g): $grand += $g['subtotal']; ?>
    <div class="preview-store card">
      <h4><?= htmlspecialchars($g['store_name']) ?></h4>
      <?php foreach ($g['items'] as $it): ?>
        <div class="preview-row">
          <span>
            <strong><?= htmlspecialchars($it['product']['product_name']) ?></strong>
            <span class="text-muted">
              · <?= (int)$it['required_packages'] ?> × pack (<?= number_format((float)$it['product']['package_quantity'], 0) ?> <?= htmlspecialchars($it['product']['package_unit']) ?>)
              — for <?= number_format($it['scaled_quantity'], 2) ?> <?= htmlspecialchars($it['unit']) ?> <?= htmlspecialchars($it['ingredient_name']) ?>
            </span>
          </span>
          <span>RM <?= number_format($it['line_total'], 2) ?></span>
        </div>
      <?php endforeach; ?>
      <div class="preview-row"><strong>Subtotal</strong><strong>RM <?= number_format($g['subtotal'], 2) ?></strong></div>
    </div>
  <?php endforeach; ?>

  <div class="cart-summary">
    <div class="line total"><span>Grand total</span><span>RM <?= number_format($grand, 2) ?></span></div>
    <form method="post" action="<?= BASE_URL ?>/recipes/<?= (int)$recipe['recipe_id'] ?>/confirm-cart" class="mt-2">
      <?= Csrf::field() ?>
      <input type="hidden" name="servings" value="<?= (int)$servings ?>">
      <button class="btn btn-primary btn-block">Add all to cart</button>
    </form>
  </div>
<?php endif; ?>

<p class="mt-2"><a href="<?= BASE_URL ?>/recipes/<?= (int)($recipe['recipe_id'] ?? 0) ?>">← Back to recipe</a></p>
