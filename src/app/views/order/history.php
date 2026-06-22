<h2>Order history</h2>
<?php if (!$orders): ?>
  <div class="card text-center text-muted">No orders yet.</div>
<?php else: ?>
  <div class="order-history-list">
    <?php foreach ($orders as $o): ?>
      <?php
        $status = (string) ($o['display_order_status'] ?? $o['order_status'] ?? 'pending');
        $statusClass = match ($status) {
            'completed', 'delivered' => 'badge-success',
            'cancelled' => 'badge-danger',
            'pending', 'accepted', 'preparing', 'ready_to_deliver', 'out_for_delivery', 'processing' => 'badge-warning',
            default => '',
        };
        $statusLabel = ucwords(str_replace('_', ' ', $status));
        $itemCount = (int) ($o['item_count'] ?? 0);
      ?>
      <article class="card order-card">
        <div class="order-card-header">
          <div>
            <span class="order-card-label">Order identifier</span>
            <h3>#<?= (int)$o['order_id'] ?></h3>
          </div>
          <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
        </div>

        <div class="order-card-details">
          <div>
            <span>Order date</span>
            <strong><?= htmlspecialchars($o['created_at']) ?></strong>
          </div>
          <div>
            <span>Items</span>
            <strong><?= $itemCount ?> item<?= $itemCount === 1 ? '' : 's' ?></strong>
          </div>
          <div>
            <span>Total amount</span>
            <strong>RM <?= number_format((float)$o['total_amount'], 2) ?></strong>
          </div>
        </div>

        <div class="order-card-footer">
          <span class="text-muted">Payment: <?= htmlspecialchars($o['payment_status']) ?></span>
          <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/orders/<?= (int)$o['order_id'] ?>">View details</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
