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
      <div style="font-size:2rem"><?= htmlspecialchars($c['category_icon'] ?: '🏷️') ?></div>
      <h4><?= htmlspecialchars($c['category_name']) ?></h4>
      <span class="badge"><?= htmlspecialchars($c['status']) ?></span>
      <div class="flex mt-2" style="justify-content:center; align-items:flex-start;">
        <details>
          <summary class="btn btn-outline btn-sm">Edit</summary>
          <form method="post" action="<?= BASE_URL ?>/admin/categories/<?= (int)$c['category_id'] ?>/update" class="mt-2">
            <?= Csrf::field() ?>
            <div class="form-row"><label>Name</label><input name="category_name" value="<?= htmlspecialchars($c['category_name']) ?>" required></div>
            <div class="form-row"><label>Icon</label><input name="category_icon" value="<?= htmlspecialchars((string)$c['category_icon']) ?>"></div>
            <button class="btn btn-primary btn-sm">Update</button>
          </form>
        </details>
        <form method="post" action="<?= BASE_URL ?>/admin/categories/<?= (int)$c['category_id'] ?>/delete" data-confirm="Deactivate?">
          <?= Csrf::field() ?>
          <button class="btn btn-danger btn-sm">Deactivate</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</div>
