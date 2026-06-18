<h2>Order #<?= (int)$order['order_id'] ?></h2>
<p class="text-muted">Placed <?= htmlspecialchars($order['created_at']) ?> · Status <?= htmlspecialchars($order['display_order_status'] ?? $order['order_status']) ?> · Payment <?= htmlspecialchars($order['payment_status']) ?></p>

<?php include __DIR__ . '/_order-details.php'; ?>
