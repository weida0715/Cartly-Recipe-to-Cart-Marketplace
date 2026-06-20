<div class="card store-hero">
  <div class="flex-between">
    <div>
      <p class="hero-eyebrow">Merchant store</p>
      <h2><?= htmlspecialchars($store['store_name']) ?></h2>
      <p class="text-muted"><?= htmlspecialchars($store['store_address'] ?? '') ?></p>
    </div>
    <div class="store-hero-badges">
      <span class="badge badge-success"><?= (int) count($store['products'] ?? []) ?> products</span>
      <span class="badge badge-warning"><?= (int) count($store['vouchers'] ?? []) ?> vouchers</span>
      <span class="badge"><?= number_format((float) ($store['rating'] ?? 0), 1) ?> rating</span>
    </div>
  </div>
  <p class="mt-2"><?= nl2br(htmlspecialchars($store['store_description'] ?? '')) ?></p>
</div>

<div class="store-detail-layout">
  <section class="store-products-column">
    <h3>Store products</h3>
    <?php if (empty($store['products'])): ?>
      <div class="card text-muted">No active products available from this store.</div>
    <?php else: ?>
      <div class="product-grid store-product-grid">
        <?php foreach ($store['products'] as $product): ?>
          <div class="product-card">
            <div class="thumb">
              <?php if (!empty($product['image'])): ?>
                <img src="<?= UPLOAD_URL ?>/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
              <?php else: ?>
                🥕
              <?php endif; ?>
            </div>
            <div class="body">
              <div class="name"><?= htmlspecialchars($product['product_name']) ?></div>
              <div class="meta"><?= htmlspecialchars($product['category_name'] ?? '') ?></div>
              <div class="meta"><?= number_format((float) $product['package_quantity'], 0) ?> <?= htmlspecialchars($product['package_unit']) ?></div>
              <div class="price">RM <?= number_format((float) $product['price'], 2) ?></div>
              <div class="actions">
                <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/products/<?= (int) $product['product_id'] ?>">Details</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <section id="store-vouchers" class="store-vouchers-column">
    <h3>Store vouchers</h3>
    <?php if (empty($store['vouchers'])): ?>
      <div class="card text-muted">No active vouchers available for this store right now.</div>
    <?php else: ?>
      <div class="voucher-grid store-voucher-grid">
        <?php foreach ($store['vouchers'] as $voucher): ?>
          <article class="voucher-card">
            <div class="voucher-card-top">
              <span class="voucher-code"><?= htmlspecialchars($voucher['voucher_code']) ?></span>
              <span class="badge"><?= htmlspecialchars($voucher['discount_type']) ?></span>
            </div>
            <h3>
              <?php if ($voucher['discount_type'] === 'percentage'): ?>
                <?= number_format((float) $voucher['discount_value'], 0) ?>% off
              <?php else: ?>
                RM <?= number_format((float) $voucher['discount_value'], 2) ?> off
              <?php endif; ?>
            </h3>
            <p>Minimum spend: RM <?= number_format((float) $voucher['minimum_spend'], 2) ?></p>
            <p class="text-muted">
              Valid until <?= $voucher['end_date'] ? htmlspecialchars($voucher['end_date']) : 'no end date' ?>
              <?php if ((int) $voucher['usage_limit'] > 0): ?>
                · <?= max(0, (int) $voucher['usage_limit'] - (int) $voucher['used_count']) ?> left
              <?php endif; ?>
            </p>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</div>
