<h2>Welcome, <?= htmlspecialchars($store['store_name']) ?></h2>
<div class="stat-grid">
  <div class="stat"><div class="num"><?= (int)$totals['orders'] ?></div><div class="label">Total orders</div></div>
  <div class="stat"><div class="num">RM <?= number_format((float)$totals['revenue'], 2) ?></div><div class="label">Revenue</div></div>
  <div class="stat"><div class="num"><?= (int)$totals['products'] ?></div><div class="label">Products</div></div>
  <div class="stat"><div class="num"><?= (int)$totals['low_stock'] ?></div><div class="label">Low stock</div></div>
</div>
<div class="card">
  <h3>Recent orders</h3>
  <?php if (!$orders): ?><p class="text-muted">No orders yet.</p>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>#</th><th>Customer</th><th>Date</th><th>Subtotal</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td>#<?= (int)$o['merchant_order_id'] ?></td>
          <td><?= htmlspecialchars($o['username']) ?></td>
          <td><?= htmlspecialchars($o['created_at']) ?></td>
          <td>RM <?= number_format((float)$o['subtotal'], 2) ?></td>
          <td><span class="badge"><?= htmlspecialchars($o['status']) ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
