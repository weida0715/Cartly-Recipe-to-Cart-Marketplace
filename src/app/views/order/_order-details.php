<?php foreach ($merchantOrders as $mo): ?>
  <div class="card">
    <h3><?= htmlspecialchars($mo['store_name']) ?> <span class="badge"><?= htmlspecialchars($mo['status']) ?></span></h3>
    <table class="table">
      <thead>
        <tr>
          <th>Item</th>
          <th>Unit</th>
          <th>Qty</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mo['items'] as $it): ?>
          <tr>
            <td><?= htmlspecialchars($it['product_name_snapshot'] ?? $it['product_name'] ?? '') ?></td>
            <td>RM <?= number_format((float) $it['unit_price'], 2) ?></td>
            <td><?= (int) $it['quantity'] ?></td>
            <td>RM <?= number_format((float) ($it['subtotal'] ?? ((float) $it['unit_price'] * (int) $it['quantity'])), 2) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="flex-between">
      <span>Subtotal: RM <?= number_format((float) $mo['subtotal'], 2) ?></span>
      <span>Discount: RM <?= number_format((float) $mo['discount_amount'], 2) ?></span>
      <strong>Total: RM
        <?= number_format((float) ($mo['final_amount'] ?? ((float) $mo['subtotal'] - (float) $mo['discount_amount'])), 2) ?></strong>
    </div>
  </div>
<?php endforeach; ?>

<p><strong>Shipping to:</strong> <?= htmlspecialchars($order['shipping_address']) ?> ·
  <?= htmlspecialchars($order['contact_phone']) ?></p>