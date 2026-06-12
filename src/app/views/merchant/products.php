<?php use App\Helpers\Csrf; ?>
<div class="flex-between mb-2">
  <h2>Products</h2>
  <a class="btn btn-primary" href="<?= BASE_URL ?>/merchant/products/create">+ New product</a>
</div>
<?php if (!$products): ?><div class="card text-muted">No products yet.</div>
<?php else: ?>
<table class="table">
  <thead><tr><th>Name</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($products as $p): ?>
    <tr>
      <td><?= htmlspecialchars($p['product_name']) ?></td>
      <td>RM <?= number_format((float)$p['price'], 2) ?></td>
      <td><?= (int)$p['stock_quantity'] ?></td>
      <td><span class="badge"><?= htmlspecialchars($p['status']) ?></span></td>
      <td>
        <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/merchant/products/<?= (int)$p['product_id'] ?>/edit">Edit</a>
        <form method="post" action="<?= BASE_URL ?>/merchant/products/<?= (int)$p['product_id'] ?>/delete" style="display:inline" data-confirm="Deactivate this product?">
          <?= Csrf::field() ?>
          <button class="btn btn-danger btn-sm">Deactivate</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
