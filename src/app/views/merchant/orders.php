<?php use App\Helpers\Csrf; ?>
<h2>Orders</h2>
<?php if (!$orders): ?>
  <div class="card text-muted">No orders.</div>
<?php else:
  foreach ($orders as $o):
    $displayStatus = (string) ($o['status'] ?? 'pending');
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
    <div class="card">
      <div class="flex-between">
        <strong>Order #<?= (int) $o['merchant_order_id'] ?> · <?= htmlspecialchars($o['username']) ?></strong>
        <span class="text-muted"><?= htmlspecialchars($o['created_at']) ?></span>
      </div>
      <div class="tracking-card <?= $displayStatus === 'cancelled' ? 'is-cancelled' : '' ?>"
        data-tracking-status="<?= htmlspecialchars($displayStatus) ?>"
        data-tracking-step="<?= $trackingStep ?>"
        data-tracking-url="<?= BASE_URL ?>/merchant/orders/<?= (int) $o['merchant_order_id'] ?>/tracking">
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
      </div>
      <ul>
        <?php foreach ($o['items'] as $it): ?>
          <li><?= htmlspecialchars($it['product_name_snapshot'] ?? $it['product_name'] ?? '') ?> × <?= (int) $it['quantity'] ?>
            — RM <?= number_format((float) ($it['subtotal'] ?? ((float) $it['unit_price'] * (int) $it['quantity'])), 2) ?></li>
        <?php endforeach; ?>
      </ul>
      <p><strong>Status:</strong> <span class="badge" data-tracking-badge><?= htmlspecialchars(str_replace('_', ' ', $displayStatus)) ?></span></p>
      <div class="flex">
      <?php if ($displayStatus === 'pending'): ?>
        <form method="post" action="<?= BASE_URL ?>/merchant/orders/<?= (int) $o['merchant_order_id'] ?>/status" data-confirm="Accept this order? This action cannot be undone.">
          <?= Csrf::field() ?>
          <input type="hidden" name="status" value="accepted">
          <button class="btn btn-primary btn-sm">Accept order</button>
        </form>
        <form method="post" action="<?= BASE_URL ?>/merchant/orders/<?= (int) $o['merchant_order_id'] ?>/status" data-confirm="Cancel this order? This action cannot be undone.">
          <?= Csrf::field() ?>
          <input type="hidden" name="status" value="cancelled">
          <button class="btn btn-danger btn-sm">Cancel order</button>
        </form>
      <?php elseif ($displayStatus === 'accepted'): ?>
        <form method="post" action="<?= BASE_URL ?>/merchant/orders/<?= (int) $o['merchant_order_id'] ?>/status" data-confirm="Start preparing this order? This action cannot be undone.">
          <?= Csrf::field() ?>
          <input type="hidden" name="status" value="preparing">
          <button class="btn btn-outline btn-sm">Prepare order</button>
        </form>
      <?php elseif ($displayStatus === 'preparing'): ?>
        <form method="post" action="<?= BASE_URL ?>/merchant/orders/<?= (int) $o['merchant_order_id'] ?>/status" data-confirm="Mark this order as ready to deliver? This action cannot be undone.">
          <?= Csrf::field() ?>
          <input type="hidden" name="status" value="ready_to_deliver">
          <button class="btn btn-primary btn-sm">Ready to deliver</button>
        </form>
      <?php else: ?>
        <span class="text-muted">No merchant action available.</span>
      <?php endif; ?>
      </div>
      <?php if (!empty($o['return_requests'])): ?>
        <section class="merchant-return-requests" aria-label="Return and refund requests">
          <h3>Return and refund requests</h3>
          <?php foreach ($o['return_requests'] as $request): ?>
            <article class="return-request-card" id="return-request-<?= (int) $request['return_request_id'] ?>">
              <div class="flex-between">
                <strong><?= htmlspecialchars((string) $request['product_name_snapshot']) ?></strong>
                <span class="badge"><?= htmlspecialchars(str_replace('_', ' ', (string) $request['status'])) ?></span>
              </div>
              <p><?= htmlspecialchars(ucfirst((string) $request['request_type'])) ?> · <?= (int) $request['quantity'] ?> item(s) · Maximum RM <?= number_format((float) $request['requested_amount'], 2) ?></p>
              <p><strong>Customer reason:</strong> <?= nl2br(htmlspecialchars((string) $request['reason'])) ?></p>
              <?php if ((string) $request['status'] === 'pending'): ?>
                <form method="post" action="<?= BASE_URL ?>/merchant/returns/<?= (int) $request['return_request_id'] ?>/decide" class="return-decision-form">
                  <?= Csrf::field() ?>
                  <label>Decision
                    <select name="decision" required>
                      <option value="refund">Approve refund</option>
                      <option value="return">Approve return</option>
                      <option value="reject">Reject request</option>
                    </select>
                  </label>
                  <label>Refund amount
                    <input type="number" name="refund_amount" min="0" max="<?= htmlspecialchars((string) $request['requested_amount']) ?>" step="0.01" value="<?= htmlspecialchars((string) $request['requested_amount']) ?>">
                  </label>
                  <label>Merchant note
                    <textarea name="merchant_note" maxlength="1000" placeholder="Required when rejecting"></textarea>
                  </label>
                  <button class="btn btn-primary btn-sm">Submit decision</button>
                </form>
              <?php elseif ((string) $request['status'] === 'return_shipped'): ?>
                <form method="post" action="<?= BASE_URL ?>/merchant/returns/<?= (int) $request['return_request_id'] ?>/receive" data-confirm="Confirm the returned item arrived? This records the refund.">
                  <?= Csrf::field() ?>
                  <button class="btn btn-primary btn-sm">Item received and refund</button>
                </form>
              <?php else: ?>
                <?php if (!empty($request['merchant_note'])): ?><p><strong>Merchant note:</strong> <?= nl2br(htmlspecialchars((string) $request['merchant_note'])) ?></p><?php endif; ?>
                <?php if (!empty($request['refund_amount'])): ?><p><strong>Refund:</strong> RM <?= number_format((float) $request['refund_amount'], 2) ?></p><?php endif; ?>
              <?php endif; ?>
            </article>
          <?php endforeach; ?>
        </section>
      <?php endif; ?>
    </div>
  <?php endforeach; endif; ?>
