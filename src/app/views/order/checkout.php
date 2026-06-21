<?php use App\Helpers\Csrf; ?>
<h2>Checkout</h2>
<form method="post" action="<?= BASE_URL ?>/checkout">
  <?= Csrf::field() ?>
  <div class="grid grid-2">
    <div class="card">
      <h3>Shipping details</h3>
      <div class="form-row"><label>Full name</label><input value="<?= htmlspecialchars($user['full_name']) ?>" disabled></div>
      <div class="form-row"><label>Shipping address</label><textarea name="shipping_address" required></textarea></div>
      <div class="form-row"><label>Contact phone</label><input name="contact_phone" type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="20" required></div>
      <div class="form-row">
        <label>Payment method</label>
        <select name="payment_method">
          <option value="simulated_card">Card (simulated)</option>
          <option value="simulated_cod">Cash on delivery (simulated)</option>
        </select>
      </div>
    </div>
    <div>
      <?php $grand = 0; foreach ($groups as $sid => $g): $grand += $g['subtotal']; ?>
        <div class="card">
          <h4><?= htmlspecialchars($g['store_name']) ?></h4>
          <?php foreach ($g['items'] as $it): ?>
            <div class="line flex-between">
              <span><?= htmlspecialchars($it['product_name']) ?> × <?= (int)$it['quantity'] ?></span>
              <span>RM <?= number_format((float)$it['line_total'], 2) ?></span>
            </div>
          <?php endforeach; ?>
          <div class="form-row mt-2">
            <label>Voucher code (this store)</label>
            <input name="voucher[<?= (int)$sid ?>]" list="voucher-options-<?= (int)$sid ?>"
              placeholder="Search available vouchers for <?= htmlspecialchars($g['store_name'] ?? ($g['items'][0]['store_name'] ?? 'this store')) ?>">
            <datalist id="voucher-options-<?= (int)$sid ?>">
              <?php foreach (($g['vouchers'] ?? []) as $voucher): ?>
                <option value="<?= htmlspecialchars($voucher['voucher_code']) ?>">
                  <?php if ($voucher['discount_type'] === 'percentage'): ?>
                    <?= number_format((float) $voucher['discount_value'], 0) ?>% off
                  <?php else: ?>
                    RM <?= number_format((float) $voucher['discount_value'], 2) ?> off
                  <?php endif; ?>
                  · min RM <?= number_format((float) $voucher['minimum_spend'], 2) ?>
                </option>
              <?php endforeach; ?>
            </datalist>
            <small class="text-muted">
              <?php if (!empty($g['vouchers'])): ?>
                Showing vouchers valid for this merchant and subtotal only.
              <?php else: ?>
                No vouchers currently apply to this merchant subtotal.
              <?php endif; ?>
            </small>
          </div>
          <div class="line flex-between"><strong>Subtotal</strong><strong>RM <?= number_format((float)$g['subtotal'], 2) ?></strong></div>
        </div>
      <?php endforeach; ?>
      <div class="cart-summary">
        <div class="line total"><span>Grand total</span><span>RM <?= number_format($grand, 2) ?></span></div>
        <button class="btn btn-primary btn-block mt-2">Place order</button>
      </div>
    </div>
  </div>
</form>
