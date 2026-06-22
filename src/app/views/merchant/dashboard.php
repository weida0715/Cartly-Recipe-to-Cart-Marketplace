<h2><?= \App\Helpers\Icon::render('store', 'heading-icon') ?>Welcome, <?= htmlspecialchars($store['store_name'] ?? 'Merchant') ?></h2>
<div class="stat-grid">
  <div class="stat"><?= \App\Helpers\Icon::render('vouchers', 'stat-icon') ?><div class="num">RM <?= number_format((float) ($totals['revenue'] ?? 0), 2) ?></div><div class="label">Revenue</div><div class="indicator <?= ($revenueChange ?? 0) >= 0 ? 'up' : 'down' ?>"><?= ($revenueChange ?? 0) >= 0 ? '▲' : '▼' ?> <?= number_format(abs((float) ($revenueChange ?? 0)), 1) ?>% vs previous 30 days</div></div>
  <div class="stat"><?= \App\Helpers\Icon::render('orders', 'stat-icon') ?><div class="num"><?= (int) ($totals['orders'] ?? 0) ?></div><div class="label">Orders received</div></div>
  <div class="stat"><?= \App\Helpers\Icon::render('products', 'stat-icon') ?><div class="num"><?= (int) ($totals['products'] ?? 0) ?></div><div class="label">Products managed</div></div>
  <div class="stat"><?= \App\Helpers\Icon::render('cart', 'stat-icon') ?><div class="num">RM <?= number_format((float) ($totals['average_order_value'] ?? 0), 2) ?></div><div class="label">Average order value</div></div>
</div>
<div class="chart-grid">
  <section class="card chart-card">
    <h3>Weekly Sale</h3>
    <div class="d3-chart" data-chart="bar" data-value-prefix="RM " data-chart-values='<?= htmlspecialchars(json_encode($salesChart ?? []), ENT_QUOTES) ?>'></div>
  </section>
  <section class="card chart-card">
    <h3>Order trend</h3>
    <div class="d3-chart" data-chart="line" data-chart-values='<?= htmlspecialchars(json_encode($orderTrendChart ?? []), ENT_QUOTES) ?>'></div>
  </section>
</div>
<div class="card">
  <h3><?= \App\Helpers\Icon::render('orders', 'heading-icon') ?>Recent orders</h3>
  <?php if (!$orders): ?><p class="text-muted">No orders yet.</p>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>#</th><th>Customer</th><th>Items</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td>#<?= (int)$o['merchant_order_id'] ?></td>
          <td><?= htmlspecialchars($o['username']) ?></td>
          <td><?= (int)($o['item_count'] ?? 0) ?></td>
          <td><?= htmlspecialchars($o['created_at']) ?></td>
          <td>RM <?= number_format((float)$o['subtotal'] - (float)$o['discount_amount'], 2) ?></td>
          <td><span class="badge"><?= htmlspecialchars($o['status']) ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
