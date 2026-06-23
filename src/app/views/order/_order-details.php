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
      <?php if (in_array($displayStatus, ['pending', 'accepted', 'preparing', 'ready_to_deliver'], true)): ?>
        <form method="post" action="<?= BASE_URL ?>/orders/merchant/<?= (int) $mo['merchant_order_id'] ?>/cancel"
          class="order-cancel-form" data-confirm="Cancel this store order before dispatch? Stock will be restored.">
          <?= Csrf::field() ?>
          <button class="btn btn-danger btn-sm">Cancel order</button>
        </form>
      <?php elseif (in_array($displayStatus, ['out_for_delivery', 'delivered', 'completed'], true)): ?>
        <div class="order-cancel-form">
          <button class="btn btn-danger btn-sm" type="button" disabled
            title="Orders cannot be cancelled after dispatch">Cancel order</button>
          <small class="text-muted">Cancellation is unavailable after dispatch.</small>
        </div>
      <?php endif; ?>
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
          <th>After-sale</th>
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
            <td>
              <?php $returnRequest = $it['return_request'] ?? null; ?>
              <?php if ($returnRequest): ?>
                <div class="return-status-summary">
                  <span class="badge"><?= htmlspecialchars(str_replace('_', ' ', (string) $returnRequest['status'])) ?></span>
                  <small><?= htmlspecialchars(ucfirst((string) $returnRequest['request_type'])) ?> request for <?= (int) $returnRequest['quantity'] ?> item(s)</small>
                  <?php if (!empty($returnRequest['refund_amount'])): ?>
                    <strong>RM <?= number_format((float) $returnRequest['refund_amount'], 2) ?></strong>
                  <?php endif; ?>
                  <?php if ((string) $returnRequest['status'] === 'return_approved'): ?>
                    <form method="post" action="<?= BASE_URL ?>/orders/returns/<?= (int) $returnRequest['return_request_id'] ?>/ship"
                      data-confirm="Confirm that you arranged and shipped this return?">
                      <?= Csrf::field() ?>
                      <button class="btn btn-primary btn-sm">Mark return shipped</button>
                    </form>
                  <?php endif; ?>
                </div>
              <?php elseif ($displayStatus === 'completed'): ?>
                <details class="return-request-form">
                  <summary>Request return/refund</summary>
                  <form method="post" action="<?= BASE_URL ?>/orders/items/<?= (int) $it['order_item_id'] ?>/return-request">
                    <?= Csrf::field() ?>
                    <label>Request type
                      <select name="request_type" required>
                        <option value="refund">Refund only</option>
                        <option value="return">Return and refund</option>
                      </select>
                    </label>
                    <label>Quantity
                      <input type="number" name="quantity" min="1" max="<?= (int) $it['quantity'] ?>" value="1" required>
                    </label>
                    <label>Reason
                      <textarea name="reason" maxlength="1000" required></textarea>
                    </label>
                    <button class="btn btn-outline btn-sm">Submit request</button>
                  </form>
                </details>
              <?php else: ?>
                <span class="text-muted">Available after completion</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="flex-between">
      <span>Subtotal: RM <?= number_format((float) $mo['subtotal'], 2) ?></span>
      <span>Discount: RM <?= number_format((float) $mo['discount_amount'], 2) ?></span>
      <span>Delivery: RM <?= number_format((float) ($mo['delivery_fee'] ?? 0), 2) ?></span>
      <strong>Total: RM
        <?= number_format((float) ($mo['final_amount'] ?? (max(0.0, (float) $mo['subtotal'] - (float) $mo['discount_amount']) + (float) ($mo['delivery_fee'] ?? 0))), 2) ?></strong>
    </div>
  </div>
<?php endforeach; ?>

<p><strong>Shipping to:</strong> <?= htmlspecialchars($order['shipping_address']) ?> ·
  <?= htmlspecialchars($order['contact_phone']) ?></p>
