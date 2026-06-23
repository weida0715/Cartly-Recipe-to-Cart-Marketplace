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
      <div class="mock-payment" data-mock-payment>
        <p class="text-muted">Mock checkout only. Cartly validates the details but never stores full card numbers or CVV values.</p>
        <div class="form-row">
          <label for="payment-method">Payment method</label>
          <select id="payment-method" name="payment_method" data-payment-method required>
            <option value="card">Credit or debit card</option>
            <option value="online_banking">Online banking (FPX)</option>
            <option value="ewallet">E-wallet</option>
          </select>
        </div>

        <div class="payment-fields" data-payment-fields="card">
          <div class="form-row"><label>Cardholder name</label><input name="cardholder_name" maxlength="100" autocomplete="cc-name"></div>
          <div class="form-row"><label>Card number</label><input name="card_number" inputmode="numeric" maxlength="23" autocomplete="cc-number" placeholder="4242 4242 4242 4242"></div>
          <div class="form-grid">
            <div class="form-row"><label>Expiry (MM/YY)</label><input name="card_expiry" maxlength="5" autocomplete="cc-exp" placeholder="12/30"></div>
            <div class="form-row"><label>CVV</label><input name="card_cvv" type="password" inputmode="numeric" maxlength="4" autocomplete="cc-csc" placeholder="123"></div>
          </div>
          <small class="text-muted">Use 4242 4242 4242 4242 for approval or 4000 0000 0000 0002 to simulate a decline.</small>
        </div>

        <div class="payment-fields" data-payment-fields="online_banking" hidden>
          <div class="form-row"><label>Account holder name</label><input name="bank_account_name" maxlength="100"></div>
          <div class="form-row"><label>Bank</label><select name="bank_name">
            <option value="">Choose bank</option>
            <option value="maybank">Maybank</option>
            <option value="cimb">CIMB Bank</option>
            <option value="public_bank">Public Bank</option>
            <option value="rhb">RHB Bank</option>
          </select></div>
        </div>

        <div class="payment-fields" data-payment-fields="ewallet" hidden>
          <div class="form-row"><label>Wallet owner name</label><input name="ewallet_name" maxlength="100"></div>
          <div class="form-row"><label>E-wallet</label><select name="ewallet_provider">
            <option value="">Choose e-wallet</option>
            <option value="touch_n_go">Touch 'n Go</option>
            <option value="grabpay">GrabPay</option>
            <option value="boost">Boost</option>
          </select></div>
          <div class="form-row"><label>Registered phone</label><input name="ewallet_phone" inputmode="numeric" maxlength="12"></div>
        </div>
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
