<?php include __DIR__ . '/header.php'; ?>
<div class="portal">
  <aside class="sidebar">
    <h3>Merchant</h3>
    <nav>
      <a<?= $navAttributes('/merchant', false) ?> href="<?= BASE_URL ?>/merchant">Dashboard</a>
      <a<?= $navAttributes('/merchant/products') ?> href="<?= BASE_URL ?>/merchant/products">Products</a>
      <a<?= $navAttributes('/merchant/orders') ?> href="<?= BASE_URL ?>/merchant/orders">Orders</a>
      <a<?= $navAttributes('/merchant/vouchers') ?> href="<?= BASE_URL ?>/merchant/vouchers">Vouchers</a>
      <a<?= $navAttributes('/merchant/store') ?> href="<?= BASE_URL ?>/merchant/store">Store Profile</a>
    </nav>
  </aside>
  <section class="portal-content">
    <?= $content ?? '' ?>
  </section>
</div>
<?php include __DIR__ . '/footer.php'; ?>
