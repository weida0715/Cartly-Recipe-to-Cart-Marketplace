<h2>Available vouchers</h2>
<p class="text-muted">Find active merchant vouchers and copy the code to apply at checkout.</p>

<form class="filters" method="get" action="<?= BASE_URL ?>/vouchers" data-search-reset>
  <input class="input" type="search" name="q" value="<?= htmlspecialchars($q) ?>"
    placeholder="Search voucher code or merchant..." autocomplete="off" data-search-reset-input>
  <select class="input" name="type">
    <option value="">All discount types</option>
    <option value="fixed" <?= $type === 'fixed' ? 'selected' : '' ?>>Fixed amount</option>
    <option value="percentage" <?= $type === 'percentage' ? 'selected' : '' ?>>Percentage</option>
  </select>
  <select class="input" name="sort">
    <?php foreach ([
      'newest' => 'Newest',
      'value_desc' => 'Discount: High to Low',
      'min_asc' => 'Minimum Spend: Low to High',
      'ending' => 'Ending Soon',
    ] as $value => $label): ?>
      <option value="<?= htmlspecialchars($value) ?>" <?= $sort === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn btn-primary" type="submit">Filter</button>
</form>

<?php if (!$vouchers): ?>
  <div class="card text-center text-muted">No available vouchers match your filter.</div>
<?php else: ?>
  <div class="voucher-grid">
    <?php foreach ($vouchers as $v): ?>
      <article class="voucher-card">
        <div class="voucher-card-top">
          <span class="voucher-code"><?= htmlspecialchars($v['voucher_code']) ?></span>
          <span class="badge"><?= htmlspecialchars($v['discount_type']) ?></span>
        </div>
        <h3>
          <?php if ($v['discount_type'] === 'percentage'): ?>
            <?= number_format((float) $v['discount_value'], 0) ?>% off
          <?php else: ?>
            RM <?= number_format((float) $v['discount_value'], 2) ?> off
          <?php endif; ?>
        </h3>
        <p class="text-muted"><?= htmlspecialchars($v['store_name']) ?></p>
        <p>Minimum spend: RM <?= number_format((float) $v['minimum_spend'], 2) ?></p>
        <p class="text-muted">
          Valid until <?= $v['end_date'] ? htmlspecialchars($v['end_date']) : 'no end date' ?>
          <?php if ((int) $v['usage_limit'] > 0): ?>
            · <?= max(0, (int) $v['usage_limit'] - (int) $v['used_count']) ?> left
          <?php endif; ?>
        </p>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>