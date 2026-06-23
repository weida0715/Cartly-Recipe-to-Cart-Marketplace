<h2>Order #<?= (int)$order['order_id'] ?></h2>
<p class="text-muted">Placed <?= htmlspecialchars($order['created_at']) ?> · Status <?= htmlspecialchars($order['display_order_status'] ?? $order['order_status']) ?> · Payment <?= htmlspecialchars($order['payment_status']) ?></p>
<div class="flex receipt-actions">
  <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/orders/<?= (int) $order['order_id'] ?>/receipt">View receipt</a>
  <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/orders/<?= (int) $order['order_id'] ?>/receipt/download">Download receipt</a>
</div>

<?php include __DIR__ . '/_order-details.php'; ?>
