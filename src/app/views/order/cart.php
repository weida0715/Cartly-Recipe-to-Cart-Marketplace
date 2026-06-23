<?php use App\Helpers\Csrf; ?>
<h2>Your cart <span class="text-muted">(<?= (int)$count ?> items)</span></h2>

<?php if (!$count): ?>
  <div class="card text-center">
    <p class="text-muted">Your cart is empty.</p>
    <a class="btn btn-primary" href="<?= BASE_URL ?>/products">Browse marketplace</a>
  </div>
<?php else: ?>
  <div class="grid grid-2">
    <div>
      <?php foreach ($groups as $sid => $g): ?>
        <div class="merchant-group card">
          <h3><?= htmlspecialchars($g['store_name']) ?></h3>
          <table class="table cart-table">
            <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($g['items'] as $it): ?>
                <?php
                  $stock = (int) $it['stock_quantity'];
                  $packageQuantity = rtrim(rtrim(number_format((float) $it['package_quantity'], 2, '.', ''), '0'), '.');
                ?>
                <tr class="cart-row">
                  <td class="cart-item-cell">
                    <div class="cart-item">
                      <div class="cart-item-thumb" aria-hidden="true">
                        <?php if (!empty($it['image'])): ?>
                          <img src="<?= UPLOAD_URL ?>/<?= htmlspecialchars($it['image']) ?>" alt="<?= htmlspecialchars($it['product_name']) ?>">
                        <?php else: ?>
                          <span class="thumb-fallback">Item</span>
                        <?php endif; ?>
                      </div>
                      <div class="cart-item-copy">
                        <div class="cart-item-title-row">
                          <strong class="cart-item-title"><?= htmlspecialchars($it['product_name']) ?></strong>
                          <?php if ($it['added_method'] === 'recipe'): ?>
                            <span class="badge badge-success">recipe</span>
                          <?php endif; ?>
                        </div>
                        <div class="cart-item-meta">
                          <?php if ((float) $it['package_quantity'] > 0): ?>
                            <span>Pack: <?= htmlspecialchars($packageQuantity) ?> <?= htmlspecialchars($it['package_unit'] ?? '') ?></span>
                          <?php endif; ?>
                          <span>Unit price: RM <?= number_format((float)$it['unit_price'], 2) ?></span>
                        </div>
                        <small class="text-muted">Stock available: <?= $stock ?><?= $stock === 0 ? ' · remove this item to continue' : '' ?></small>
                      </div>
                    </div>
                  </td>
                  <td class="cart-qty-cell">
                    <span class="cart-mobile-label">Qty</span>
                    <form method="post" action="<?= BASE_URL ?>/cart/update" class="cart-qty-form">
                      <?= Csrf::field() ?>
                      <input type="hidden" name="cart_item_id" value="<?= (int)$it['cart_item_id'] ?>">
                      <input class="qty" type="number" name="quantity" value="<?= (int)$it['quantity'] ?>" min="1" max="<?= max(1, $stock) ?>" <?= $stock === 0 ? 'disabled' : '' ?>>
                      <button class="btn btn-outline btn-sm" <?= $stock === 0 ? 'disabled' : '' ?>>Update</button>
                    </form>
                    <small class="text-muted">Quantity in cart</small>
                  </td>
                  <td class="cart-price-cell">
                    <span class="cart-mobile-label">Total</span>
                    <strong>RM <?= number_format((float)$it['line_total'], 2) ?></strong>
                    <small class="text-muted"><?= (int)$it['quantity'] ?> × RM <?= number_format((float)$it['unit_price'], 2) ?></small>
                  </td>
                  <td class="cart-action-cell">
                    <span class="cart-mobile-label">Action</span>
                    <form method="post" action="<?= BASE_URL ?>/cart/remove" data-confirm="Remove this item?">
                      <?= Csrf::field() ?>
                      <input type="hidden" name="cart_item_id" value="<?= (int)$it['cart_item_id'] ?>">
                      <button class="btn btn-danger btn-sm">Remove</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <tr class="cart-subtotal-row"><td colspan="3" class="text-right"><strong>Subtotal</strong></td><td><strong>RM <?= number_format((float)$g['subtotal'], 2) ?></strong></td></tr>
            </tbody>
          </table>
        </div>
      <?php endforeach; ?>
      <form method="post" action="<?= BASE_URL ?>/cart/clear" data-confirm="Clear entire cart?">
        <?= Csrf::field() ?>
        <button class="btn btn-outline">Clear cart</button>
      </form>
    </div>
    <div>
      <div class="cart-summary">
        <h3>Order summary</h3>
        <div class="line"><span>Items</span><span><?= (int)$count ?></span></div>
        <div class="line total"><span>Total</span><span>RM <?= number_format((float)$total, 2) ?></span></div>
        <a class="btn btn-primary btn-block mt-2" href="<?= BASE_URL ?>/checkout">Proceed to checkout</a>
      </div>
    </div>
  </div>
<?php endif; ?>
