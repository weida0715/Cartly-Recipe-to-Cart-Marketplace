<?php
use App\Helpers\AuthHelper;
$user = AuthHelper::user();
$role = AuthHelper::role();
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
        <a href="<?= BASE_URL ?>/products">Marketplace</a>
        <a href="<?= BASE_URL ?>/stores">Stores</a>
        <a href="<?= BASE_URL ?>/recipes">Recipes</a>
        <?php if ($user): ?>
          <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
          <a href="<?= BASE_URL ?>/cart">Cart</a>
          <a href="<?= BASE_URL ?>/orders">Orders</a>
          <a href="<?= BASE_URL ?>/saved-recipes">Saved</a>
          <a href="<?= BASE_URL ?>/profile">Profile</a>
          <?php if ($role === 'merchant'): ?>
            <a href="<?= BASE_URL ?>/merchant">Merchant</a>
          <?php endif; ?>
          <?php if ($role === 'admin'): ?>
            <a href="<?= BASE_URL ?>/admin">Admin</a>
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
