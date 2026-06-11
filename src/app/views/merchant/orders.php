<?php use App\Helpers\Csrf; ?>
<h2>Orders</h2>
<?php if (!$orders): ?>
  <div class="card text-muted">No orders.</div>
<?php else:
  foreach ($orders as $o): ?>
    <div class="card">
      <div class="flex-between">
        <strong>Order #<?= (int) $o['merchant_order_id'] ?> · <?= htmlspecialchars($o['username']) ?></strong>
        <span class="text-muted"><?= htmlspecialchars($o['created_at']) ?></span>
      </div>
      <ul>
        <?php foreach ($o['items'] as $it): ?>
          <li><?= htmlspecialchars($it['product_name_snapshot'] ?? $it['product_name'] ?? '') ?> × <?= (int) $it['quantity'] ?>
            — RM <?= number_format((float) ($it['subtotal'] ?? ((float) $it['unit_price'] * (int) $it['quantity'])), 2) ?></li>
        <?php endforeach; ?>
      </ul>
      <form method="post" action="<?= BASE_URL ?>/merchant/orders/<?= (int) $o['merchant_order_id'] ?>/status" class="flex">
        <?= Csrf::field() ?>
        <select name="status">
          <?php foreach (['pending', 'accepted', 'preparing', 'completed', 'cancelled'] as $s): ?>
            <option <?= $o['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-primary btn-sm">Update</button>
      </form>
    </div>
  <?php endforeach; endif; ?>