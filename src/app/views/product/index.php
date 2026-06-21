<?php use App\Helpers\Csrf; ?>
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
          <?php
          $reviewRating = (float) ($p['review_rating'] ?? 0);
          $displayRating = $reviewRating > 0 ? $reviewRating : (float) ($p['rating'] ?? 0);
          $reviewCount = (int) ($p['review_count'] ?? 0);
          $inStock = (int) ($p['stock_quantity'] ?? 0) > 0;
          ?>
          <div class="product-card">
            <a class="product-thumb-link" href="<?= BASE_URL ?>/products/<?= (int) $p['product_id'] ?>"
              aria-label="View <?= htmlspecialchars($p['product_name']) ?> details">
              <div class="thumb">
                <?php if (!empty($p['image'])): ?>
                  <img src="<?= htmlspecialchars(UPLOAD_URL . '/' . ltrim((string) $p['image'], '/'), ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($p['product_name']) ?>">
                <?php else: ?>
                  <?= \App\Helpers\Icon::render('marketplace', 'product-fallback-icon') ?>
                <?php endif; ?>
              </div>
            </a>
            <div class="body">
              <div class="product-card-topline">
                <span class="product-category"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorised') ?></span>
                <span class="product-rating" aria-label="<?= number_format($displayRating, 1) ?> out of 5 stars">
                  <span aria-hidden="true">&#9733;</span>
                  <?= number_format($displayRating, 1) ?>
                  <?php if ($reviewCount > 0): ?>
                    <span class="product-review-count">(<?= $reviewCount ?>)</span>
                  <?php endif; ?>
                </span>
              </div>
              <div class="name"><?= htmlspecialchars($p['product_name']) ?></div>
              <div class="meta">
                Sold by <a href="<?= BASE_URL ?>/stores/<?= (int) $p['store_id'] ?>"><?= htmlspecialchars($p['store_name']) ?></a>
              </div>
              <div class="product-package">
                <?= number_format((float) $p['package_quantity'], 0) ?>
                <?= htmlspecialchars($p['package_unit']) ?> per pack
              </div>
              <div class="price">RM <?= number_format((float) $p['price'], 2) ?></div>
              <div class="product-stock <?= $inStock ? 'is-available' : 'is-unavailable' ?>">
                <?= $inStock ? (int) $p['stock_quantity'] . ' available' : 'Out of stock' ?>
              </div>
              <div class="actions">
                <a class="btn btn-outline btn-sm"
                  href="<?= BASE_URL ?>/products/<?= (int) $p['product_id'] ?>">Details</a>
                <?php if ($inStock): ?>
                  <form method="post" action="<?= BASE_URL ?>/cart/add">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="product_id" value="<?= (int) $p['product_id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-primary btn-sm" type="submit">
                      <?= \App\Helpers\Icon::render('cart', 'button-icon') ?>Add to cart
                    </button>
                  </form>
                <?php else: ?>
                  <button class="btn btn-primary btn-sm" type="button" disabled>Out of stock</button>
                <?php endif; ?>
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
