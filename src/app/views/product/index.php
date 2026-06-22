<div class="marketplace-heading">
  <h2>Marketplace</h2>
  <?php if (\App\Helpers\AuthHelper::check()): ?>
    <a class="marketplace-cart" href="<?= BASE_URL ?>/cart"
      aria-label="View cart, <?= (int) $cartCount ?> <?= (int) $cartCount === 1 ? 'item' : 'items' ?>">
      <span class="marketplace-cart-icon" aria-hidden="true">&#128722;</span>
      <span class="marketplace-cart-count" aria-hidden="true"><?= (int) $cartCount ?></span>
    </a>
  <?php endif; ?>
</div>
<div class="marketplace-layout">
  <aside class="marketplace-filter-column" aria-label="Marketplace filters">
    <form class="filters marketplace-filters" method="get" action="<?= BASE_URL ?>/products" data-search-reset>
      <div class="marketplace-filter-heading">
        <h3>Filters</h3>
        <?php if ($q !== '' || $cid !== null || ($sort ?? 'newest') !== 'newest'): ?>
          <a href="<?= BASE_URL ?>/products">Reset</a>
        <?php endif; ?>
      </div>

      <div class="form-row">
        <label for="product-search">Search</label>
        <input class="input" id="product-search" type="search" name="q" value="<?= htmlspecialchars($q) ?>"
          placeholder="Products or ingredients" autocomplete="off" data-search-reset-input>
      </div>

      <div class="form-row">
        <label for="product-category">Category</label>
        <select class="input" id="product-category" name="cid">
          <option value="">All categories</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= (int) $c['category_id'] ?>" <?= $cid === (int) $c['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['category_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-row">
        <label for="product-sort">Sort by</label>
        <select class="input" id="product-sort" name="sort">
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
      </div>

      <button class="btn btn-primary btn-block" type="submit">Apply filters</button>
    </form>
  </aside>

  <section class="marketplace-results" aria-labelledby="marketplace-results-title">
    <div class="marketplace-results-heading">
      <h3 id="marketplace-results-title">Products</h3>
      <span><?= (int) $total ?> <?= (int) $total === 1 ? 'result' : 'results' ?></span>
    </div>

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
                &#129365;
              <?php endif; ?>
            </div>
            <div class="body">
              <div class="name"><?= htmlspecialchars($p['product_name']) ?></div>
              <div class="meta">
                <a href="<?= BASE_URL ?>/stores/<?= (int) $p['store_id'] ?>"><?= htmlspecialchars($p['store_name']) ?></a>
                &middot; <?= htmlspecialchars($p['category_name'] ?? '') ?>
              </div>
              <div class="meta"><?= number_format((float) $p['package_quantity'], 0) ?>
                <?= htmlspecialchars($p['package_unit']) ?>
              </div>
              <div class="price">RM <?= number_format((float) $p['price'], 2) ?></div>
              <div class="actions">
                <a class="btn btn-outline btn-sm"
                  href="<?= BASE_URL ?>/products/<?= (int) $p['product_id'] ?>">Details</a>
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
      <div class="pagination marketplace-pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <?php $query['page'] = $i; ?>
          <a class="btn <?= $i === (int) $page ? 'btn-primary' : 'btn-outline' ?> btn-sm"
            href="<?= BASE_URL ?>/products?<?= htmlspecialchars(http_build_query($query)) ?>"><?= $i ?></a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </section>
</div>
