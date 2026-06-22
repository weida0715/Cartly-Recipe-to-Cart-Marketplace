<?php $stats = is_array($stats ?? null) ? $stats : []; ?>
<h2><?= \App\Helpers\Icon::render('admin', 'heading-icon') ?>Admin dashboard</h2>
<div class="stat-grid">
  <div class="stat"><?= \App\Helpers\Icon::render('users', 'stat-icon') ?><div class="num"><?= (int) ($stats['users'] ?? 0) ?></div><div class="label">Users</div></div>
  <div class="stat"><?= \App\Helpers\Icon::render('profile', 'stat-icon') ?><div class="num"><?= (int) ($stats['customers'] ?? 0) ?></div><div class="label">Customers</div></div>
  <div class="stat"><?= \App\Helpers\Icon::render('merchant', 'stat-icon') ?><div class="num"><?= (int) ($stats['merchants'] ?? 0) ?></div><div class="label">Merchants</div></div>
  <div class="stat"><?= \App\Helpers\Icon::render('orders', 'stat-icon') ?><div class="num"><?= (int) ($stats['pending'] ?? 0) ?></div><div class="label">Pending stores</div></div>
  <div class="stat"><?= \App\Helpers\Icon::render('store', 'stat-icon') ?><div class="num"><?= (int) ($stats['stores'] ?? 0) ?></div><div class="label">Active stores</div></div>
  <div class="stat"><?= \App\Helpers\Icon::render('products', 'stat-icon') ?><div class="num"><?= (int) ($stats['products'] ?? 0) ?></div><div class="label">Products</div></div>
  <div class="stat"><?= \App\Helpers\Icon::render('cart', 'stat-icon') ?><div class="num"><?= (int) ($stats['orders'] ?? 0) ?></div><div class="label">Orders</div></div>
  <div class="stat"><?= \App\Helpers\Icon::render('reports', 'stat-icon') ?><div class="num"><?= (int) ($stats['reports'] ?? 0) ?></div><div class="label">Open reports</div></div>
</div>

<div class="chart-grid">
  <section class="card chart-card">
    <h3>User roles</h3>
    <div class="d3-chart" data-chart="pie" data-chart-values='<?= htmlspecialchars(json_encode([
      ['label' => 'Customers', 'value' => (int) ($stats['customers'] ?? 0)],
      ['label' => 'Merchants', 'value' => (int) ($stats['merchants'] ?? 0)],
    ]), ENT_QUOTES) ?>'></div>
  </section>
  <section class="card chart-card">
    <h3>Platform overview</h3>
    <div class="d3-chart" data-chart="bar" data-chart-values='<?= htmlspecialchars(json_encode([
      ['label' => 'Stores', 'value' => (int) ($stats['stores'] ?? 0)],
      ['label' => 'Products', 'value' => (int) ($stats['products'] ?? 0)],
      ['label' => 'Orders', 'value' => (int) ($stats['orders'] ?? 0)],
      ['label' => 'Reports', 'value' => (int) ($stats['reports'] ?? 0)],
    ]), ENT_QUOTES) ?>'></div>
  </section>
</div>
