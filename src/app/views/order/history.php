<h2>Order history</h2>
<?php if (!$orders): ?>
  <div class="card text-center text-muted">No orders yet.</div>
<?php else: ?>
  <table class="table">
    <thead><tr><th>#</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td>#<?= (int)$o['order_id'] ?></td>
          <td><?= htmlspecialchars($o['created_at']) ?></td>
          <td>RM <?= number_format((float)$o['total_amount'], 2) ?></td>
          <td><?= htmlspecialchars($o['payment_status']) ?></td>
          <td><?= htmlspecialchars($o['order_status']) ?></td>
          <td><a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/orders/<?= (int)$o['order_id'] ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
