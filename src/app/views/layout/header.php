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
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/auth.css">
  <link rel="stylesheet" href="/assets/css/marketplace.css">
  <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>

<body>
  <header class="site-header">
    <div class="container nav">
      <a class="brand" href="/">🛒 Cartly</a>
      <nav class="nav-links">
        <a href="/products">Marketplace</a>
        <a href="/recipes">Recipes</a>
        <?php if ($user): ?>
          <a href="/dashboard">Dashboard</a>
          <a href="/cart">Cart</a>
          <a href="/orders">Orders</a>
          <a href="/saved-recipes">Saved</a>
          <a href="/profile">Profile</a>
          <?php if ($role === 'merchant'): ?>
            <a href="/merchant">Merchant</a>
          <?php endif; ?>
          <?php if ($role === 'admin'): ?>
            <a href="/admin">Admin</a>
          <?php endif; ?>
          <span class="user-chip">Hi, <?= htmlspecialchars($user['username']) ?></span>
          <a class="btn btn-ghost" href="/auth/logout">Logout</a>
        <?php else: ?>
          <a class="btn btn-ghost" href="/auth/login">Login</a>
          <a class="btn btn-primary" href="/auth/register">Register</a>
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