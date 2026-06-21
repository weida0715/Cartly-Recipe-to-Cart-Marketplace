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
    </div>
  <?php endforeach; endif; ?>
