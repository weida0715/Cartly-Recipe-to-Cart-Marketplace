<?php use App\Helpers\Csrf; ?>
<h2>Categories</h2>
<div class="card">
  <h3>Add new</h3>
  <form method="post" action="<?= BASE_URL ?>/admin/categories" class="form-grid" enctype="multipart/form-data">
    <?= Csrf::field() ?>
    <div class="form-row"><label>Name</label><input name="category_name" required></div>
    <div class="form-row">
      <label>Category image</label>
      <input type="file" name="category_icon" accept="image/*" required>
    </div>
    <div class="form-row" style="align-self:end"><button class="btn btn-primary">Add</button></div>
  </form>
</div>
<div class="grid grid-3">
  <?php foreach ($cats as $c): ?>
    <?php $dialogId = 'category-edit-' . (int) $c['category_id']; ?>
    <div class="card text-center">
      <?php $icon = (string) ($c['category_icon'] ?? ''); ?>
      <?php $hasIconImage = $icon !== '' && (str_contains($icon, '/') || preg_match('/\.(png|jpe?g|gif|webp)$/i', $icon)); ?>
      <?php if ($hasIconImage): ?>
        <img src="<?= UPLOAD_URL ?>/<?= htmlspecialchars(ltrim($icon, '/')) ?>"
          alt="<?= htmlspecialchars($c['category_name']) ?>"
          style="width:72px;height:72px;object-fit:contain;display:block;margin:0 auto 0.5rem;">
      <?php else: ?>
        <div style="font-size:2rem">🏷️</div>
      <?php endif; ?>
      <h4><?= htmlspecialchars($c['category_name']) ?></h4>
      <span class="badge"><?= htmlspecialchars($c['status']) ?></span>
      <div class="flex mt-2" style="justify-content:center; align-items:flex-start;">
        <button type="button" class="btn btn-outline btn-sm" data-category-edit-open data-dialog-target="<?= htmlspecialchars($dialogId) ?>">Edit</button>
        <form method="post" action="<?= BASE_URL ?>/admin/categories/<?= (int)$c['category_id'] ?>/delete" data-confirm="Deactivate?">
          <?= Csrf::field() ?>
          <button class="btn btn-danger btn-sm">Deactivate</button>
        </form>
      </div>
    </div>
    <dialog class="category-dialog" id="<?= htmlspecialchars($dialogId) ?>" data-category-edit-dialog>
      <form method="post" action="<?= BASE_URL ?>/admin/categories/<?= (int)$c['category_id'] ?>/update" enctype="multipart/form-data">
        <?= Csrf::field() ?>
        <div class="dialog-header">
          <h3>Edit category</h3>
          <button type="button" class="btn btn-ghost btn-sm" data-dialog-close aria-label="Close">Close</button>
        </div>
        <div class="form-row"><label>Name</label><input name="category_name" value="<?= htmlspecialchars($c['category_name']) ?>" required></div>
        <div class="form-row">
          <label>Category image</label>
          <?php if ($hasIconImage): ?>
            <img src="<?= UPLOAD_URL ?>/<?= htmlspecialchars(ltrim($icon, '/')) ?>"
              alt="<?= htmlspecialchars($c['category_name']) ?>"
              style="width:64px;height:64px;object-fit:contain;display:block;margin:.5rem 0;">
          <?php endif; ?>
          <input type="file" name="category_icon" accept="image/*">
        </div>
        <div class="dialog-actions">
          <button type="button" class="btn btn-outline btn-sm" data-dialog-close>Cancel</button>
          <button class="btn btn-primary btn-sm">Update</button>
        </div>
      </form>
    </dialog>
  <?php endforeach; ?>
</div>
