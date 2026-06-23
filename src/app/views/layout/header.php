<?php
use App\Helpers\AuthHelper;
$user = AuthHelper::user();
$role = AuthHelper::role();
$notificationPreview = [];
$unreadNotificationCount = 0;
if ($user) {
  try {
    $notificationModel = new \App\Models\Notification();
    $notificationPreview = $notificationModel->latestForUser((int) $user['user_id'], 8);
    $unreadNotificationCount = $notificationModel->unreadCount((int) $user['user_id']);
  } catch (\Throwable) {
    // Keep navigation available until the notification migration is imported.
  }
}

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$normalizedBaseUrl = rtrim(BASE_URL, '/');
if ($normalizedBaseUrl !== '' && ($requestPath === $normalizedBaseUrl || str_starts_with($requestPath, $normalizedBaseUrl . '/'))) {
  $requestPath = substr($requestPath, strlen($normalizedBaseUrl)) ?: '/';
}
$requestPath = '/' . trim($requestPath, '/');

$isActivePath = static function (string $path, bool $includeChildren = true) use ($requestPath): bool {
  $path = '/' . trim($path, '/');

  return $requestPath === $path
    || ($includeChildren && str_starts_with($requestPath, $path . '/'));
};

$navAttributes = static function (string $path, bool $includeChildren = true) use ($isActivePath): string {
  return $isActivePath($path, $includeChildren)
    ? ' class="active" aria-current="page"'
    : '';
};
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Cartly') ?></title>
  <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/css/style.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/css/auth.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/css/marketplace.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/css/dashboard.css">
</head>

<body>
  <header class="site-header">
    <div class="container nav">
      <a class="brand" href="<?= BASE_URL ?>/"><?= \App\Helpers\Icon::render('cart', 'brand-icon') ?> Cartly</a>
      <nav class="nav-links">
        <a href="<?= BASE_URL ?>/products"><?= \App\Helpers\Icon::render('marketplace', 'nav-icon') ?>Marketplace</a>
        <a href="<?= BASE_URL ?>/stores"><?= \App\Helpers\Icon::render('store', 'nav-icon') ?>Stores</a>
        <a href="<?= BASE_URL ?>/recipes"><?= \App\Helpers\Icon::render('recipes', 'nav-icon') ?>Recipes</a>
        <?php if ($user): ?>
          <a<?= $navAttributes('/dashboard', false) ?> href="<?= BASE_URL ?>/dashboard"><?= \App\Helpers\Icon::render('dashboard', 'nav-icon') ?>Dashboard</a>
          <a<?= $navAttributes('/cart') ?> href="<?= BASE_URL ?>/cart"><?= \App\Helpers\Icon::render('cart', 'nav-icon') ?>Cart</a>
          <a<?= $navAttributes('/orders') ?> href="<?= BASE_URL ?>/orders"><?= \App\Helpers\Icon::render('orders', 'nav-icon') ?>Orders</a>
          <a<?= $navAttributes('/saved-recipes', false) ?> href="<?= BASE_URL ?>/saved-recipes"><?= \App\Helpers\Icon::render('saved', 'nav-icon') ?>Saved</a>
          <a<?= $navAttributes('/profile', false) ?> href="<?= BASE_URL ?>/profile"><?= \App\Helpers\Icon::render('profile', 'nav-icon') ?>Profile</a>
          <?php if ($role === 'merchant'): ?>
            <a<?= $navAttributes('/merchant') ?> href="<?= BASE_URL ?>/merchant"><?= \App\Helpers\Icon::render('merchant', 'nav-icon') ?>Merchant</a>
          <?php endif; ?>
          <?php if ($role === 'admin'): ?>
            <a<?= $navAttributes('/admin') ?> href="<?= BASE_URL ?>/admin"><?= \App\Helpers\Icon::render('admin', 'nav-icon') ?>Admin</a>
          <?php endif; ?>
          <span class="user-chip">Hi, <?= htmlspecialchars($user['username']) ?></span>
          <details class="notification-menu">
            <summary class="notification-trigger" aria-label="Notifications">
              <?= \App\Helpers\Icon::render('notifications', 'nav-icon') ?>
              <?php if ($unreadNotificationCount > 0): ?>
                <span class="notification-count"><?= min(99, $unreadNotificationCount) ?></span>
              <?php endif; ?>
            </summary>
            <div class="notification-dropdown">
              <div class="notification-dropdown-header">
                <strong>Notifications</strong>
                <span><?= (int) $unreadNotificationCount ?> unread</span>
              </div>
              <?php if (!$notificationPreview): ?>
                <p class="notification-empty">No notifications yet.</p>
              <?php else: ?>
                <div class="notification-dropdown-list">
                  <?php foreach ($notificationPreview as $notification): ?>
                    <a class="notification-preview <?= empty($notification['is_read']) ? 'is-unread' : '' ?>"
                      href="<?= BASE_URL ?>/notifications/<?= (int) $notification['notification_id'] ?>">
                      <strong><?= htmlspecialchars((string) $notification['title']) ?></strong>
                      <span><?= htmlspecialchars((string) $notification['message']) ?></span>
                      <small><?= htmlspecialchars((string) $notification['created_at']) ?></small>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <a class="notification-view-all" href="<?= BASE_URL ?>/notifications">View all notifications</a>
            </div>
          </details>
          <a class="btn btn-ghost" href="<?= BASE_URL ?>/auth/logout"><?= \App\Helpers\Icon::render('logout', 'nav-icon') ?>Logout</a>
        <?php else: ?>
          <a class="btn btn-ghost" href="<?= BASE_URL ?>/auth/login"><?= \App\Helpers\Icon::render('login', 'nav-icon') ?>Login</a>
          <a class="btn btn-primary" href="<?= BASE_URL ?>/auth/register"><?= \App\Helpers\Icon::render('register', 'nav-icon') ?>Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  <main class="container main-content">
    <?php
    $flashes = \App\Helpers\Flash::pull();
    foreach ($flashes as $f): ?>
      <div class="flash flash-<?= htmlspecialchars($f['type']) ?>"><?= htmlspecialchars($f['message']) ?></div>
    <?php endforeach; ?>
