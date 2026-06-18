<?php
use App\Helpers\AuthHelper;
$user = AuthHelper::user();
$role = AuthHelper::role();

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if (BASE_URL !== '' && ($requestPath === BASE_URL || str_starts_with($requestPath, BASE_URL . '/'))) {
  $requestPath = substr($requestPath, strlen(BASE_URL)) ?: '/';
}
$requestPath = '/' . trim($requestPath, '/');
if ($requestPath !== '/') {
  $requestPath = rtrim($requestPath, '/');
}

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
  <link rel="stylesheet" href="<?= ASSET_URL ?>/css/style.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/css/auth.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/css/marketplace.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/css/dashboard.css">
</head>

<body>
  <header class="site-header">
    <div class="container nav">
      <a class="brand" href="<?= BASE_URL ?>/">🛒 Cartly</a>
      <nav class="nav-links">
        <a<?= $navAttributes('/products') ?> href="<?= BASE_URL ?>/products">Marketplace</a>
        <a<?= $navAttributes('/recipes') ?> href="<?= BASE_URL ?>/recipes">Recipes</a>
        <?php if ($user): ?>
          <a<?= $navAttributes('/dashboard', false) ?> href="<?= BASE_URL ?>/dashboard">Dashboard</a>
          <a<?= $navAttributes('/cart') ?> href="<?= BASE_URL ?>/cart">Cart</a>
          <a<?= $navAttributes('/orders') ?> href="<?= BASE_URL ?>/orders">Orders</a>
          <a<?= $navAttributes('/saved-recipes', false) ?> href="<?= BASE_URL ?>/saved-recipes">Saved</a>
          <a<?= $navAttributes('/profile', false) ?> href="<?= BASE_URL ?>/profile">Profile</a>
          <?php if ($role === 'merchant'): ?>
            <a<?= $navAttributes('/merchant') ?> href="<?= BASE_URL ?>/merchant">Merchant</a>
          <?php endif; ?>
          <?php if ($role === 'admin'): ?>
            <a<?= $navAttributes('/admin') ?> href="<?= BASE_URL ?>/admin">Admin</a>
          <?php endif; ?>
          <span class="user-chip">Hi, <?= htmlspecialchars($user['username']) ?></span>
          <a class="btn btn-ghost" href="<?= BASE_URL ?>/auth/logout">Logout</a>
        <?php else: ?>
          <a class="btn btn-ghost" href="<?= BASE_URL ?>/auth/login">Login</a>
          <a class="btn btn-primary" href="<?= BASE_URL ?>/auth/register">Register</a>
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
