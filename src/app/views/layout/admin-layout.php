<?php include __DIR__ . '/header.php'; ?>
<div class="portal">
  <aside class="sidebar admin">
    <h3>Admin</h3>
    <nav>
      <a<?= $navAttributes('/admin', false) ?> href="<?= BASE_URL ?>/admin">Dashboard</a>
      <a<?= $navAttributes('/admin/merchants') ?> href="<?= BASE_URL ?>/admin/merchants">Merchant Approval</a>
      <a<?= $navAttributes('/admin/users') ?> href="<?= BASE_URL ?>/admin/users">Users</a>
      <a<?= $navAttributes('/admin/categories') ?> href="<?= BASE_URL ?>/admin/categories">Categories</a>
      <a<?= $navAttributes('/admin/reports') ?> href="<?= BASE_URL ?>/admin/reports">Reports</a>
    </nav>
  </aside>
  <section class="portal-content">
    <?= $content ?? '' ?>
  </section>
</div>
<?php include __DIR__ . '/footer.php'; ?>
