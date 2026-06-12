<?php include __DIR__ . '/header.php'; ?>
<div class="portal">
  <aside class="sidebar">
    <h3>Merchant</h3>
    <nav>
      <a href="<?= BASE_URL ?>/merchant">Dashboard</a>
      <a href="<?= BASE_URL ?>/merchant/products">Products</a>
      <a href="<?= BASE_URL ?>/merchant/orders">Orders</a>
      <a href="<?= BASE_URL ?>/merchant/vouchers">Vouchers</a>
      <a href="<?= BASE_URL ?>/merchant/store">Store Profile</a>
    </nav>
  </aside>
  <section class="portal-content">
    <?= $content ?? '' ?>
  </section>
</div>
<?php include __DIR__ . '/footer.php'; ?>
