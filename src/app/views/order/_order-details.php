<?php use App\Helpers\Csrf; ?>
<?php foreach ($merchantOrders as $mo): ?>
  <div class="card">
    <?php
      $displayStatus = (string) ($mo['persisted_tracking_status'] ?? $mo['status']);
      $trackingSteps = ['Order placed', $displayStatus === 'cancelled' ? 'Merchant cancelled' : 'Merchant accepted', 'Merchant preparing', 'Ready to deliver', 'Out for delivery', 'Delivered', 'Order complete'];
      $trackingStep = match ($displayStatus) {
        'pending' => 1,
        'accepted' => 2,
        'preparing' => 3,
        'ready_to_deliver' => 4,
        'out_for_delivery' => 5,
        'delivered' => 6,
        'completed' => 7,
        'cancelled' => 2,
        default => 1,
      };
    ?>
    <h3><?= htmlspecialchars($mo['store_name']) ?> <span class="badge" data-tracking-badge><?= htmlspecialchars(str_replace('_', ' ', $displayStatus)) ?></span></h3>
      <div class="tracking-card <?= $displayStatus === 'cancelled' ? 'is-cancelled' : '' ?>"
        data-tracking-status="<?= htmlspecialchars($displayStatus) ?>"
        data-tracking-step="<?= $trackingStep ?>"
        data-tracking-url="<?= BASE_URL ?>/orders/merchant/<?= (int) $mo['merchant_order_id'] ?>/tracking">
        <div class="tracking-header">
          <strong>Delivery tracking</strong>
          <span data-tracking-label><?= htmlspecialchars($trackingSteps[$trackingStep - 1]) ?></span>
        </div>
        <div class="tracking-steps">
          <i class="tracking-line"></i>
          <i class="tracking-fill" data-tracking-fill style="width: calc((100% - (100% / 7)) * <?= ($trackingStep - 1) / 6 ?>);"></i>
          <?php foreach ($trackingSteps as $i => $step): ?>
            <span class="<?= $i < $trackingStep ? 'is-done' : '' ?>" data-tracking-dot><?= htmlspecialchars($step) ?></span>
          <?php endforeach; ?>
        </div>
        <?php if ($displayStatus === 'ready_to_deliver'): ?>
          <form method="post" action="<?= BASE_URL ?>/orders/merchant/<?= (int) $mo['merchant_order_id'] ?>/advance" data-auto-advance="10000" hidden>
            <?= Csrf::field() ?>
            <input type="hidden" name="status" value="out_for_delivery">
          </form>
        <?php elseif ($displayStatus === 'out_for_delivery'): ?>
          <form method="post" action="<?= BASE_URL ?>/orders/merchant/<?= (int) $mo['merchant_order_id'] ?>/advance" data-auto-advance="10000" hidden>
            <?= Csrf::field() ?>
            <input type="hidden" name="status" value="delivered">
          </form>
        <?php elseif ($displayStatus === 'delivered'): ?>
          <form method="post" action="<?= BASE_URL ?>/orders/merchant/<?= (int) $mo['merchant_order_id'] ?>/received" class="mt-2" data-confirm="Confirm that you received this order?">
            <?= Csrf::field() ?>
            <button class="btn btn-primary btn-sm">Received</button>
          </form>
        <?php endif; ?>
        <?php if (in_array($displayStatus, ['ready_to_deliver', 'out_for_delivery'], true)): ?>
          <form method="post" action="<?= BASE_URL ?>/orders/merchant/<?= (int) $mo['merchant_order_id'] ?>/received" class="mt-2" data-received-form hidden data-confirm="Confirm that you received this order?">
            <?= Csrf::field() ?>
            <button class="btn btn-primary btn-sm">Received</button>
          </form>
        <?php endif; ?>
      </div>
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