<?php
$type = (string) ($notification['type'] ?? 'info');
$actionUrl = (string) ($notification['action_url'] ?? '');
?>
<article class="card notification-detail">
  <div class="notification-detail-heading">
    <span class="notification-type notification-type-<?= htmlspecialchars($type) ?>"></span>
    <div>
      <p class="hero-eyebrow"><?= htmlspecialchars(ucfirst($type)) ?></p>
      <h2><?= htmlspecialchars((string) ($notification['title'] ?? 'Cartly update')) ?></h2>
      <p class="text-muted"><?= htmlspecialchars((string) ($notification['created_at'] ?? '')) ?></p>
    </div>
  </div>
  <p class="notification-detail-message"><?= nl2br(htmlspecialchars((string) ($notification['message'] ?? ''))) ?></p>
  <div class="flex">
    <?php if ($actionUrl !== '' && str_starts_with($actionUrl, '/') && !str_starts_with($actionUrl, '//')): ?>
      <a class="btn btn-primary" href="<?= htmlspecialchars(BASE_URL . $actionUrl) ?>">Open related page</a>
    <?php endif; ?>
    <a class="btn btn-outline" href="<?= BASE_URL ?>/notifications">Back to notifications</a>
  </div>
</article>
