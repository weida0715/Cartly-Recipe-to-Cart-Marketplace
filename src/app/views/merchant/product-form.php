<?php
use App\Helpers\Csrf;
$action = $product
  ? BASE_URL . '/merchant/products/' . (int) $product['product_id'] . '/update'
  : BASE_URL . '/merchant/products';
?>
<h2><?= $product ? 'Edit product' : 'New product' ?></h2>

<div class="validation-dialog" data-product-validation-dialog hidden role="alert"></div>

<form method="post" action="<?= $action ?>" class="card" enctype="multipart/form-data" data-product-form novalidate>
  <?= Csrf::field() ?>
  <div class="form-row"><label>Name</label><input name="product_name"
      value="<?= htmlspecialchars($product['product_name'] ?? '') ?>" required></div>
  <div class="form-row"><label>Description</label><textarea
      name="description"><?= htmlspecialchars($product['description'] ?? '') ?></textarea></div>
  <div class="form-row">
    <label>Product image</label>
    <?php if (!empty($product['image'])): ?>
      <div class="current-upload-preview">
        <img src="<?= UPLOAD_URL ?>/<?= htmlspecialchars($product['image']) ?>"
          alt="<?= htmlspecialchars($product['product_name'] ?? 'Current product image') ?>"
          style="max-width:180px;max-height:140px;border-radius:12px;object-fit:cover;display:block;margin:.5rem 0;">
        <p class="text-muted">Current: <?= htmlspecialchars($product['image']) ?></p>
      </div>
    <?php endif; ?>
    <input type="file" name="image" accept="image/*">
  </div>
  <div class="form-grid">
    <div class="form-row">
      <label>Category</label>
      <select name="category_id">
        <option value="">— none —</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= (int) $c['category_id'] ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $c['category_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['category_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-row">
      <label>Standard ingredient</label>
      <select name="ingredient_id">
        <option value="">— none —</option>
        <?php foreach ($ingredients as $i): ?>
          <option value="<?= (int) $i['ingredient_id'] ?>" <?= (int) ($product['ingredient_id'] ?? 0) === (int) $i['ingredient_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($i['ingredient_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="form-grid">
    <div class="form-row"><label>Price (RM)</label><input type="number" step="0.01" min="0" name="price"
        required data-validation-label="Price"
        value="<?= htmlspecialchars((string) ($product['price'] ?? '0.00')) ?>"></div>
    <div class="form-row"><label>Stock</label><input type="number" min="0" name="stock_quantity"
        required data-validation-label="Stock quantity"
        value="<?= (int) ($product['stock_quantity'] ?? 0) ?>"></div>
  </div>
  <div class="form-grid">
    <div class="form-row"><label>Package quantity</label><input type="number" step="0.01" min="0.01" name="package_quantity"
        required data-validation-label="Package quantity"
        value="<?= htmlspecialchars((string) ($product['package_quantity'] ?? '1')) ?>"></div>
    <div class="form-row"><label>Package unit</label><input name="package_unit"
        value="<?= htmlspecialchars($product['package_unit'] ?? 'g') ?>"></div>
  </div>
  <div class="form-row">
    <label>Status</label>
    <select name="status">
      <?php foreach (['active', 'inactive', 'out_of_stock'] as $s): ?>
        <option <?= ($product['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button class="btn btn-primary"><?= $product ? 'Save changes' : 'Create product' ?></button>
</form>