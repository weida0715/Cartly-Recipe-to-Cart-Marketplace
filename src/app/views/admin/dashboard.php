<h2>Admin dashboard</h2>
<div class="stat-grid">
  <div class="stat"><div class="num"><?= (int)$stats['users'] ?></div><div class="label">Users</div></div>
  <div class="stat"><div class="num"><?= (int)$stats['customers'] ?></div><div class="label">Customers</div></div>
  <div class="stat"><div class="num"><?= (int)$stats['merchants'] ?></div><div class="label">Merchants</div></div>
  <div class="stat"><div class="num"><?= (int)$stats['pending'] ?></div><div class="label">Pending stores</div></div>
  <div class="stat"><div class="num"><?= (int)$stats['stores'] ?></div><div class="label">Active stores</div></div>
  <div class="stat"><div class="num"><?= (int)$stats['products'] ?></div><div class="label">Products</div></div>
  <div class="stat"><div class="num"><?= (int)$stats['orders'] ?></div><div class="label">Orders</div></div>
  <div class="stat"><div class="num"><?= (int)$stats['reports'] ?></div><div class="label">Open reports</div></div>
</div>
