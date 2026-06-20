<h2>Marketplace</h2>
<form class="filters" method="get" action="<?= BASE_URL ?>/products" data-search-reset>
  <input class="input" id="product-search" type="search" name="q" value="<?= htmlspecialchars($q) ?>"
    placeholder="Search products or ingredients..." autocomplete="off" data-search-reset-input>
  <select class="input" name="cid">
    <option value="">All categories</option>
    <?php foreach ($cats as $c): ?>
      <option value="<?= (int) $c['category_id'] ?>" <?= $cid === (int) $c['category_id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($c['category_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <select class="input" name="sort">
    <?php foreach ([
      'newest' => 'Newest',
      'oldest' => 'Oldest',
      'price_asc' => 'Price: Low to High',
      'price_desc' => 'Price: High to Low',
      'rating_desc' => 'Rating: High to Low',
      'review_desc' => 'Reviews: Highest Rated',
      'review_count_desc' => 'Reviews: Most Reviewed',
      'name_asc' => 'Name: A to Z',
    ] as $value => $label): ?>
      <option value="<?= htmlspecialchars($value) ?>" <?= ($sort ?? 'newest') === $value ? 'selected' : '' ?>>
        <?= htmlspecialchars($label) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <button class="btn btn-primary" type="submit">Search</button>
</form>

<?php if (!$products): ?>
  <div class="card text-center text-muted">No products match your filter.</div>
<?php else: ?>
  <div class="product-grid">
    <?php foreach ($products as $p): ?>
      <div class="product-card">
        <div class="thumb">
          <?php if (!empty($p['image'])): ?>
            <img src="<?= UPLOAD_URL ?>/<?= htmlspecialchars($p['image']) ?>"
              alt="<?= htmlspecialchars($p['product_name']) ?>">
          <?php else: ?>
            🥕
          <?php endif; ?>
        </div>
        <div class="body">
          <div class="name"><?= htmlspecialchars($p['product_name']) ?></div>
          <div class="meta">
            <a href="<?= BASE_URL ?>/stores/<?= (int) $p['store_id'] ?>"><?= htmlspecialchars($p['store_name']) ?></a>
            · <?= htmlspecialchars($p['category_name'] ?? '') ?>
          </div>
          <div class="meta"><?= number_format((float) $p['package_quantity'], 0) ?>
            <?= htmlspecialchars($p['package_unit']) ?>
          </div>
          <div class="price">RM <?= number_format((float) $p['price'], 2) ?></div>
          <div class="actions">
            <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/products/<?= (int) $p['product_id'] ?>">Details</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if (!empty($pages) && $pages > 1): ?>
  <?php
  $query = $_GET;
  unset($query['page']);
  ?>
  <div class="pagination mt-3" style="display:flex;gap:.5rem;flex-wrap:wrap;justify-content:center;">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <?php $query['page'] = $i; ?>
      <a class="btn <?= $i === (int) $page ? 'btn-primary' : 'btn-outline' ?> btn-sm"
        href="<?= BASE_URL ?>/products?<?= htmlspecialchars(http_build_query($query)) ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
<?php endif; ?>
