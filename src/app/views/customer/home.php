<section class="hero hero-showcase">
  <div class="hero-copy">
    <p class="hero-eyebrow">Recipe-to-cart marketplace</p>
    <h1>Fresh ingredients. Real recipes. One cart.</h1>
    <p>Discover recipes, scale servings, and Cartly fills your basket from local merchants.</p>
    <div class="hero-actions">
      <a class="btn btn-accent" href="<?= BASE_URL ?>/recipes">Browse Recipes</a>
      <a class="btn btn-outline hero-outline" href="<?= BASE_URL ?>/products">Shop Marketplace</a>
    </div>
  </div>
  <div class="hero-panel" aria-hidden="true">
    <div class="hero-panel-top">
      <span>Dinner basket</span>
      <strong>RM 42.80</strong>
    </div>
    <div class="hero-cart-row">
      <span class="hero-cart-icon">1</span>
      <div>
        <strong>Tomato pasta kit</strong>
        <small>4 ingredients matched</small>
      </div>
    </div>
    <div class="hero-cart-row">
      <span class="hero-cart-icon">2</span>
      <div>
        <strong>Fresh greens</strong>
        <small>Local merchant stock</small>
      </div>
    </div>
    <div class="hero-panel-note">Voucher ready at checkout</div>
  </div>
</section>

<section class="promo-banner" aria-labelledby="promo-banner-title">
  <div class="promo-banner-content">
    <p class="promo-eyebrow">Merchant voucher hub</p>
    <h2 id="promo-banner-title">Browse available vouchers from local merchants</h2>
    <p>
      View active vouchers from approved stores, filter by merchant or discount type, and copy the code before checkout.
    </p>
  </div>
  <div class="promo-banner-actions">
    <a class="btn btn-accent" href="<?= BASE_URL ?>/vouchers">View Available Vouchers</a>
    <a class="btn btn-outline" href="<?= BASE_URL ?>/products">Shop Marketplace</a>
  </div>
</section>

<?php if (!empty($cats)): ?>
  <section class="mb-3 home-section">
    <div class="section-heading">
      <h2>Categories</h2>
      <p>Jump into the aisles shoppers use most.</p>
    </div>
    <div class="grid grid-4">
      <?php foreach (array_slice($cats, 0, 8) as $c): ?>
        <a class="category-tile" href="<?= BASE_URL ?>/products?cid=<?= (int) $c['category_id'] ?>">
          <span class="category-mark">Cart</span>
          <strong><?= htmlspecialchars($c['category_name']) ?></strong>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

<section class="mb-3 home-section">
  <div class="section-heading">
    <h2>Featured products</h2>
    <p>Fresh picks from approved local merchants.</p>
  </div>
  <div class="product-grid">
    <?php foreach ($featured as $p): ?>
      <div class="product-card">
        <div class="thumb">
          <?php if (!empty($p['image'])): ?>
            <img src="<?= UPLOAD_URL ?>/<?= htmlspecialchars($p['image']) ?>"
              alt="<?= htmlspecialchars($p['product_name']) ?>">
          <?php else: ?>
            <span class="thumb-fallback">Fresh</span>
          <?php endif; ?>
        </div>
        <div class="body">
          <div class="name"><?= htmlspecialchars($p['product_name']) ?></div>
          <div class="meta"><?= htmlspecialchars($p['store_name']) ?></div>
          <div class="price">RM <?= number_format((float) $p['price'], 2) ?></div>
          <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/products/<?= (int) $p['product_id'] ?>">View</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="home-section">
  <div class="section-heading">
    <h2>Recipes to try</h2>
    <p>Start with a meal idea and build the basket from there.</p>
  </div>
  <div class="recipe-grid">
    <?php foreach ($recipes as $r): ?>
      <div class="recipe-card">
        <div class="thumb">
          <?php if (!empty($r['image'])): ?>
            <img src="<?= UPLOAD_URL ?>/<?= htmlspecialchars($r['image']) ?>"
              alt="<?= htmlspecialchars($r['recipe_title']) ?>">
          <?php else: ?>
            <span class="thumb-fallback">Recipe</span>
          <?php endif; ?>
        </div>
        <div class="body">
          <h3><?= htmlspecialchars($r['recipe_title']) ?></h3>
          <div class="text-muted">
            <?= htmlspecialchars($r['cuisine_type'] ?? '') ?> &middot; <?= htmlspecialchars($r['difficulty'] ?? '') ?>
          </div>
          <a class="btn btn-primary btn-sm mt-1" href="<?= BASE_URL ?>/recipes/<?= (int) $r['recipe_id'] ?>">Open</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
