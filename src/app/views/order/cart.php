<?php use App\Helpers\Csrf; ?>
<h2>Your cart <span class="text-muted">(<?= (int) ($count ?? 0) ?> items)</span></h2>

<?php if (empty($count)): ?>
  <div class="card text-center">
    <p class="text-muted">Your cart is empty.</p>
    <a class="btn btn-primary" href="<?= BASE_URL ?>/products">Browse marketplace</a>
  </div>
<?php else: ?>
  <div class="grid grid-2 cart-layout">
    <div>
      <?php foreach (($groups ?? []) as $storeId => $group): ?>
        <section class="merchant-group card">
          <h3><?= htmlspecialchars((string) ($group['store_name'] ?? 'Merchant')) ?></h3>
          <div class="cart-table-wrap">
            <table class="table cart-table">
              <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th></th></tr></thead>
              <tbody>
                <?php foreach (($group['items'] ?? []) as $item): ?>
                  <?php $stock = (int) ($item['stock_quantity'] ?? 0); ?>
                  <tr>
                    <td>
                      <?= htmlspecialchars((string) ($item['product_name'] ?? 'Product')) ?>
                      <?php if (($item['added_method'] ?? '') === 'recipe'): ?>
                        <span class="badge badge-success">recipe</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <form method="post" action="<?= BASE_URL ?>/cart/update" class="flex">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="cart_item_id" value="<?= (int) ($item['cart_item_id'] ?? 0) ?>">
                        <input class="qty" type="number" name="quantity" value="<?= (int) ($item['quantity'] ?? 1) ?>" min="1" max="<?= max(1, $stock) ?>" <?= $stock === 0 ? 'disabled' : '' ?>>
                        <button class="btn btn-outline btn-sm" <?= $stock === 0 ? 'disabled' : '' ?>>Update</button>
                      </form>
                      <small class="text-muted">Stock: <?= $stock ?><?= $stock === 0 ? ' - remove this item to continue' : '' ?></small>
                    </td>
                    <td>RM <?= number_format((float) ($item['line_total'] ?? 0), 2) ?></td>
                    <td>
                      <form method="post" action="<?= BASE_URL ?>/cart/remove" data-confirm="Remove this item?">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="cart_item_id" value="<?= (int) ($item['cart_item_id'] ?? 0) ?>">
                        <button class="btn btn-danger btn-sm">Remove</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="cart-voucher-panel">
            <div class="cart-voucher-heading">
              <div>
                <h4>Merchant vouchers</h4>
                <p class="text-muted">Apply one or more valid vouchers to this merchant subtotal.</p>
              </div>
            </div>

            <?php if (!empty($group['applied_vouchers'])): ?>
              <div class="applied-voucher-list" aria-label="Applied vouchers">
                <?php foreach ($group['applied_vouchers'] as $voucher): ?>
                  <div class="applied-voucher">
                    <div>
                      <strong><?= htmlspecialchars((string) ($voucher['voucher_code'] ?? '')) ?></strong>
                      <span>-RM <?= number_format((float) ($voucher['discount_amount'] ?? 0), 2) ?></span>
                    </div>
                    <form method="post" action="<?= BASE_URL ?>/cart/vouchers/remove">
                      <?= Csrf::field() ?>
                      <input type="hidden" name="store_id" value="<?= (int) $storeId ?>">
                      <input type="hidden" name="voucher_code" value="<?= htmlspecialchars((string) ($voucher['voucher_code'] ?? '')) ?>">
                      <button class="btn btn-ghost btn-sm" aria-label="Remove voucher <?= htmlspecialchars((string) ($voucher['voucher_code'] ?? '')) ?>">Remove</button>
                    </form>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <form class="cart-voucher-form" method="post" action="<?= BASE_URL ?>/cart/vouchers/apply">
              <?= Csrf::field() ?>
              <input type="hidden" name="store_id" value="<?= (int) $storeId ?>">
              <label class="sr-only" for="voucher-code-<?= (int) $storeId ?>">Voucher code for <?= htmlspecialchars((string) ($group['store_name'] ?? 'merchant')) ?></label>
              <input id="voucher-code-<?= (int) $storeId ?>" name="voucher_code" list="voucher-options-<?= (int) $storeId ?>" maxlength="50" placeholder="Enter voucher code" autocomplete="off" required>
              <datalist id="voucher-options-<?= (int) $storeId ?>">
                <?php foreach (($group['available_vouchers'] ?? []) as $voucher): ?>
                  <option value="<?= htmlspecialchars((string) ($voucher['voucher_code'] ?? '')) ?>">
                    <?= ($voucher['discount_type'] ?? '') === 'percentage'
                      ? number_format((float) ($voucher['discount_value'] ?? 0), 0) . '% off'
                      : 'RM ' . number_format((float) ($voucher['discount_value'] ?? 0), 2) . ' off' ?>
                  </option>
                <?php endforeach; ?>
              </datalist>
              <button class="btn btn-primary">Apply voucher</button>
            </form>

            <div class="merchant-total-lines">
              <div class="line"><span>Subtotal</span><span>RM <?= number_format((float) ($group['subtotal'] ?? 0), 2) ?></span></div>
              <?php if ((float) ($group['discount_total'] ?? 0) > 0): ?>
                <div class="line discount"><span>Voucher savings</span><span>-RM <?= number_format((float) $group['discount_total'], 2) ?></span></div>
              <?php endif; ?>
              <div class="line total"><span>Merchant total</span><span>RM <?= number_format((float) ($group['final_total'] ?? $group['subtotal'] ?? 0), 2) ?></span></div>
            </div>
          </div>
        </section>
      <?php endforeach; ?>
      <form method="post" action="<?= BASE_URL ?>/cart/clear" data-confirm="Clear entire cart?">
        <?= Csrf::field() ?>
        <button class="btn btn-outline">Clear cart</button>
      </form>
    </div>
    <aside>
      <div class="cart-summary">
        <h3>Order summary</h3>
<<<<<<< HEAD
        <div class="line"><span>Items</span><span><?= (int) $count ?></span></div>
        <div class="line"><span>Subtotal</span><span>RM <?= number_format((float) ($subtotal ?? 0), 2) ?></span></div>
        <?php if ((float) ($discountTotal ?? 0) > 0): ?>
          <div class="line discount"><span>Voucher savings</span><span>-RM <?= number_format((float) $discountTotal, 2) ?></span></div>
        <?php endif; ?>
        <div class="line total"><span>Total</span><span>RM <?= number_format((float) ($total ?? 0), 2) ?></span></div>
=======
        <div class="line"><span>Items</span><span><?= (int)$count ?></span></div>
        <div class="line"><span>Estimated subtotal</span><span>RM <?= number_format((float)$subtotal, 2) ?></span></div>
        <div class="line"><span>Delivery cost</span><span>RM <?= number_format((float)$deliveryFee, 2) ?></span></div>
        <div class="line total"><span>Total amount</span><span>RM <?= number_format((float)$total, 2) ?></span></div>
>>>>>>> origin
        <a class="btn btn-primary btn-block mt-2" href="<?= BASE_URL ?>/checkout">Proceed to checkout</a>
      </div>
    </aside>
  </div>
<?php endif; ?>
