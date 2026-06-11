<?php use App\Helpers\Csrf; ?>
<h2>Categories</h2>
<div class="card">
  <h3>Add new</h3>
  <form method="post" action="<?= BASE_URL ?>/admin/categories" class="form-grid">
    <?= Csrf::field() ?>
    <div class="form-row"><label>Name</label><input name="category_name" required></div>
    <div class="form-row"><label>Icon (key or emoji)</label><input name="category_icon"></div>
    <div class="form-row" style="align-self:end"><button class="btn btn-primary">Add</button></div>
  </form>
</div>
<div class="grid grid-3">
  <?php foreach ($cats as $c): ?>
    <div class="card text-center">
      <div style="font-size:2rem">🏷️</div>
      <h4><?= htmlspecialchars($c['category_name']) ?></h4>
      <span class="badge"><?= htmlspecialchars($c['status']) ?></span>
      <form method="post" action="<?= BASE_URL ?>/admin/categories/<?= (int)$c['category_id'] ?>/delete" class="mt-2" data-confirm="Deactivate?">
        <?= Csrf::field() ?>
        <button class="btn btn-danger btn-sm">Deactivate</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
