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
