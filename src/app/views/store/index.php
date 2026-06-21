<h2>Merchant Stores</h2>
<p class="text-muted">Browse approved stores, shop their items, and view vouchers published by each merchant.</p>

<form class="filters" method="get" action="<?= BASE_URL ?>/stores" data-search-reset>
  <input class="input" type="search" name="q" value="<?= htmlspecialchars($q) ?>"
    placeholder="Search store name, description, or address..." autocomplete="off" data-search-reset-input>
  <button class="btn btn-primary" type="submit">Search</button>
</form>

<?php if (!$stores): ?>
  <div class="card text-center text-muted">No stores match your search.</div>
<?php else: ?>
  <div class="store-grid">
    <?php foreach ($stores as $store): ?>
      <article class="store-card">
        <div class="store-card-top">
          <div>
            <h3><?= htmlspecialchars($store['store_name']) ?></h3>
            <p class="text-muted"><?= htmlspecialchars($store['store_address'] ?? '') ?></p>
          </div>
          <span class="badge badge-success"><?= (int) $store['product_count'] ?> items</span>
        </div>
        <p><?= nl2br(htmlspecialchars($store['store_description'] ?? '')) ?></p>
        <div class="store-stats">
          <span><?= (int) $store['product_count'] ?> products</span>
          <span><?= (int) $store['voucher_count'] ?> vouchers</span>
          <span>Rating <?= number_format((float) ($store['rating'] ?? 0), 1) ?></span>
        </div>
        <?php if (!empty($store['featured_products'])): ?>
          <div class="store-featured">
            <?php foreach ($store['featured_products'] as $product): ?>
              <a class="store-featured-item" href="<?= BASE_URL ?>/products/<?= (int) $product['product_id'] ?>">
                <strong><?= htmlspecialchars($product['product_name']) ?></strong>
                <span>RM <?= number_format((float) $product['price'], 2) ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <div class="flex mt-2" style="justify-content: flex-end;">
          <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/stores/<?= (int) $store['store_id'] ?>">View store</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
