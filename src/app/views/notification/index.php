<?php use App\Helpers\Csrf; ?>
<div class="section-heading notification-page-heading">
  <div>
    <h2>Notifications</h2>
    <p>Order, report, merchant, stock, and account updates in one place.</p>
  </div>
  <?php if ($notifications): ?>
    <form method="post" action="<?= BASE_URL ?>/notifications/read-all">
      <?= Csrf::field() ?>
      <button class="btn btn-outline btn-sm">Mark all as read</button>
    </form>
  <?php endif; ?>
</div>

<?php if (!$notifications): ?>
  <div class="card text-center text-muted">No notifications yet.</div>
<?php else: ?>
  <div class="notification-list">
    <?php foreach ($notifications as $notification): ?>
      <a class="card notification-list-item <?= empty($notification['is_read']) ? 'is-unread' : '' ?>"
        href="<?= BASE_URL ?>/notifications/<?= (int) $notification['notification_id'] ?>">
        <span class="notification-type notification-type-<?= htmlspecialchars((string) $notification['type']) ?>"></span>
        <span class="notification-list-copy">
          <strong><?= htmlspecialchars((string) $notification['title']) ?></strong>
          <span><?= htmlspecialchars((string) $notification['message']) ?></span>
          <small><?= htmlspecialchars((string) $notification['created_at']) ?></small>
        </span>
        <span aria-hidden="true">›</span>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
