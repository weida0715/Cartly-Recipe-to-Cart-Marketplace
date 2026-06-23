<?php include __DIR__ . '/header.php'; ?>
<div class="portal">
  <aside class="sidebar admin">
    <h3><?= \App\Helpers\Icon::render('admin', 'heading-icon') ?>Admin</h3>
    <nav>
      <a<?= $navAttributes('/admin', false) ?> href="<?= BASE_URL ?>/admin"><?= \App\Helpers\Icon::render('dashboard', 'sidebar-icon') ?>Dashboard</a>
      <a<?= $navAttributes('/admin/merchants') ?> href="<?= BASE_URL ?>/admin/merchants"><?= \App\Helpers\Icon::render('merchant', 'sidebar-icon') ?>Merchant Approval</a>
      <a<?= $navAttributes('/admin/users') ?> href="<?= BASE_URL ?>/admin/users"><?= \App\Helpers\Icon::render('users', 'sidebar-icon') ?>Users</a>
      <a<?= $navAttributes('/admin/categories') ?> href="<?= BASE_URL ?>/admin/categories"><?= \App\Helpers\Icon::render('categories', 'sidebar-icon') ?>Categories</a>
      <a<?= $navAttributes('/admin/settings') ?> href="<?= BASE_URL ?>/admin/settings"><?= \App\Helpers\Icon::render('settings', 'sidebar-icon') ?>Settings</a>
      <a<?= $navAttributes('/admin/reports') ?> href="<?= BASE_URL ?>/admin/reports"><?= \App\Helpers\Icon::render('reports', 'sidebar-icon') ?>Reports</a>
    </nav>
  </aside>
  <section class="portal-content">
    <?= $content ?? '' ?>
  </section>
</div>
<?php include __DIR__ . '/footer.php'; ?>
