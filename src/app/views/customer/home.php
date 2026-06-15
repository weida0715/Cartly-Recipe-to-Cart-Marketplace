<section class="hero">
  <h1>Fresh ingredients. Real recipes. One cart.</h1>
  <p>Discover recipes, scale servings, and Cartly fills your basket from local merchants.</p>
  <a class="btn btn-accent" href="<?= BASE_URL ?>/recipes">Browse Recipes</a>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/products">Shop Marketplace</a>
</section>

<section class="promo-banner" aria-labelledby="promo-banner-title">
  <div class="promo-banner-content">
    <p class="promo-eyebrow">Limited weekend offer</p>
    <h2 id="promo-banner-title">Save more when recipes become a cart</h2>
    <p>
      Build a recipe cart today and apply merchant vouchers at checkout for fresh ingredients from approved local
      stores.
    </p>
  </div>
  <div class="promo-banner-actions">
    <a class="btn btn-accent" href="<?= BASE_URL ?>/recipes">Start with Recipes</a>
    <a class="btn btn-outline" href="<?= BASE_URL ?>/products">Shop Deals</a>
  </div>
</section>

<?php if (!empty($cats)): ?>
  <section class="mb-3">
    <h2>Categories</h2>
    <div class="grid grid-4">
      <?php foreach (array_slice($cats, 0, 8) as $c): ?>
        <a class="card text-center" href="<?= BASE_URL ?>/products?cid=<?= (int) $c['category_id'] ?>">
          <div style="font-size:2rem">🛒</div>
          <div><?= htmlspecialchars($c['category_name']) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

<section class="mb-3">
  <h2>Featured products</h2>
  <div class="product-grid">
    <?php foreach ($featured as $p): ?>
      <div class="product-card">
        <div class="thumb">
          <?php if (!empty($p['image'])): ?>
            <img src="<?= UPLOAD_URL ?>/<?= htmlspecialchars($p['image']) ?>"
              alt="<?= htmlspecialchars($p['product_name']) ?>">
          <?php else: ?>
            🥬
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

<section>
  <h2>Recipes to try</h2>
  <div class="recipe-grid">
    <?php foreach ($recipes as $r): ?>
      <div class="recipe-card">
        <div class="thumb">
          <?php if (!empty($r['image'])): ?>
            <img src="<?= UPLOAD_URL ?>/<?= htmlspecialchars($r['image']) ?>"
              alt="<?= htmlspecialchars($r['recipe_title']) ?>">
          <?php else: ?>
            🍳
          <?php endif; ?>
        </div>
        <div class="body">
          <h3><?= htmlspecialchars($r['recipe_title']) ?></h3>
          <div class="text-muted"><?= htmlspecialchars($r['cuisine_type']) ?> · <?= htmlspecialchars($r['difficulty']) ?>
          </div>
          <a class="btn btn-primary btn-sm mt-1" href="<?= BASE_URL ?>/recipes/<?= (int) $r['recipe_id'] ?>">Open</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
