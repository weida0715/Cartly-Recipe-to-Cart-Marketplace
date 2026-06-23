<div class="card text-center">
  <h2>🎉 Order placed!</h2>
  <p>Order <strong>#<?= (int)$order['order_id'] ?></strong> · RM <?= number_format((float)$order['total_amount'], 2) ?></p>
  <p class="text-muted">Payment: <?= htmlspecialchars($order['payment_status']) ?> · Status: <?= htmlspecialchars($order['display_order_status'] ?? $order['order_status']) ?></p>
  <a class="btn btn-primary" href="<?= BASE_URL ?>/orders/<?= (int)$order['order_id'] ?>">View order</a>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/orders/<?= (int)$order['order_id'] ?>/receipt">View receipt</a>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/orders/<?= (int)$order['order_id'] ?>/receipt/download">Download receipt</a>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/products">Keep shopping</a>
</div>

<?php include __DIR__ . '/_order-details.php'; ?>
