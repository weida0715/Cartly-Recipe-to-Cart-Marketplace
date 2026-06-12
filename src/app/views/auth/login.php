<?php
require_once dirname(__DIR__) . '/layout/header.php';
use App\Helpers\Csrf;
?>
<div class="auth-shell">
  <div class="auth-card">
    <h1>Welcome back</h1>
    <p class="sub">Sign in to your Cartly account</p>
    <div class="demo-badges">
      <span class="badge badge-success">admin</span>
      <span class="badge badge-warning">merchant</span>
      <span class="badge">customer</span>
    </div>
    <form method="post" action="<?= BASE_URL ?>/auth/login" class="mt-2">
      <?= Csrf::field() ?>
      <div class="form-row">
        <label for="login">Username or email</label>
        <input id="login" name="login" type="text" required autofocus>
      </div>
      <div class="form-row">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    <p class="help">
      <a href="<?= BASE_URL ?>/auth/forgot-password">Forgot password?</a>
      &nbsp;·&nbsp;
      No account? <a href="<?= BASE_URL ?>/auth/register">Register</a>
    </p>
  </div>
</div>
<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>
