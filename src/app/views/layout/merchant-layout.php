<?php include __DIR__ . '/header.php'; ?>
<div class="portal">
  <aside class="sidebar">
    <h3><?= \App\Helpers\Icon::render('merchant', 'heading-icon') ?>Merchant</h3>
    <nav>
      <a<?= $navAttributes('/merchant', false) ?> href="<?= BASE_URL ?>/merchant"><?= \App\Helpers\Icon::render('dashboard', 'sidebar-icon') ?>Dashboard</a>
      <a<?= $navAttributes('/merchant/products') ?> href="<?= BASE_URL ?>/merchant/products"><?= \App\Helpers\Icon::render('products', 'sidebar-icon') ?>Products</a>
      <a<?= $navAttributes('/merchant/orders') ?> href="<?= BASE_URL ?>/merchant/orders"><?= \App\Helpers\Icon::render('orders', 'sidebar-icon') ?>Orders</a>
      <a<?= $navAttributes('/merchant/vouchers') ?> href="<?= BASE_URL ?>/merchant/vouchers"><?= \App\Helpers\Icon::render('vouchers', 'sidebar-icon') ?>Vouchers</a>
      <a<?= $navAttributes('/merchant/store') ?> href="<?= BASE_URL ?>/merchant/store"><?= \App\Helpers\Icon::render('store-profile', 'sidebar-icon') ?>Store Profile</a>
    </nav>
  </aside>
  <section class="portal-content">
    <?= $content ?? '' ?>
  </section>
</div>
<?php include __DIR__ . '/footer.php'; ?>
