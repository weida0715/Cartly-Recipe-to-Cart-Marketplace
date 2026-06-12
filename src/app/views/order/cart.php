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
                <tr>
                  <td>
                    <?= htmlspecialchars($it['product_name']) ?>
                    <?php if ($it['added_method'] === 'recipe'): ?>
                      <span class="badge badge-success">recipe</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <form method="post" action="<?= BASE_URL ?>/cart/update" class="flex">
                      <?= Csrf::field() ?>
                      <input type="hidden" name="cart_item_id" value="<?= (int)$it['cart_item_id'] ?>">
                      <input class="qty" type="number" name="quantity" value="<?= (int)$it['quantity'] ?>" min="1">
                      <button class="btn btn-outline btn-sm">Update</button>
                    </form>
                  </td>
                  <td>RM <?= number_format((float)$it['line_total'], 2) ?></td>
                  <td>
                    <form method="post" action="<?= BASE_URL ?>/cart/remove" data-confirm="Remove this item?">
                      <?= Csrf::field() ?>
                      <input type="hidden" name="cart_item_id" value="<?= (int)$it['cart_item_id'] ?>">
                      <button class="btn btn-danger btn-sm">Remove</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <tr><td colspan="3" class="text-right"><strong>Subtotal</strong></td><td><strong>RM <?= number_format((float)$g['subtotal'], 2) ?></strong></td></tr>
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
