<?php use App\Helpers\Csrf; ?>
<h2>Checkout</h2>
<form method="post" action="<?= BASE_URL ?>/checkout">
  <?= Csrf::field() ?>
  <div class="grid grid-2">
    <div class="card">
      <h3>Shipping details</h3>
      <div class="form-row"><label>Full name</label><input value="<?= htmlspecialchars((string) ($user['full_name'] ?? '')) ?>" disabled></div>
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
      <?php foreach (($groups ?? []) as $group): ?>
        <section class="card checkout-merchant-summary">
          <h4><?= htmlspecialchars((string) ($group['store_name'] ?? 'Merchant')) ?></h4>
          <?php foreach (($group['items'] ?? []) as $item): ?>
            <div class="line flex-between">
              <span><?= htmlspecialchars((string) ($item['product_name'] ?? 'Product')) ?> x <?= (int) ($item['quantity'] ?? 0) ?></span>
              <span>RM <?= number_format((float) ($item['line_total'] ?? 0), 2) ?></span>
            </div>
          <?php endforeach; ?>
          <div class="line flex-between"><strong>Subtotal</strong><strong>RM <?= number_format((float) ($group['subtotal'] ?? 0), 2) ?></strong></div>
          <?php foreach (($group['applied_vouchers'] ?? []) as $voucher): ?>
            <div class="line flex-between checkout-voucher-line">
              <span>Voucher <?= htmlspecialchars((string) ($voucher['voucher_code'] ?? '')) ?></span>
              <span>-RM <?= number_format((float) ($voucher['discount_amount'] ?? 0), 2) ?></span>
            </div>
          <?php endforeach; ?>
          <?php if ((float) ($group['discount_total'] ?? 0) > 0): ?>
            <div class="line flex-between checkout-merchant-total"><strong>Merchant total</strong><strong>RM <?= number_format((float) ($group['final_total'] ?? 0), 2) ?></strong></div>
          <?php endif; ?>
        </section>
      <?php endforeach; ?>
      <div class="cart-summary">
        <div class="line"><span>Item subtotal</span><span>RM <?= number_format((float) ($subtotal ?? 0), 2) ?></span></div>
        <div class="line"><span>Delivery cost</span><span>RM <?= number_format((float) ($deliveryFee ?? 0), 2) ?></span></div>
        <?php if ((float) ($discountTotal ?? 0) > 0): ?>
          <div class="line discount"><span>Voucher savings</span><span>-RM <?= number_format((float) $discountTotal, 2) ?></span></div>
        <?php endif; ?>
        <div class="line total"><span>Total amount</span><span>RM <?= number_format((float) ($total ?? 0), 2) ?></span></div>
        <a class="btn btn-outline btn-block mt-2" href="<?= BASE_URL ?>/cart">Edit cart vouchers</a>
        <button class="btn btn-primary btn-block mt-2">Place order</button>
      </div>
    </div>
  </div>
</form>
