<?php
require_once dirname(__DIR__) . '/layout/header.php';
use App\Helpers\Csrf;
?>
<div class="auth-shell">
  <div class="auth-card">
    <h1>Create account</h1>
    <p class="sub">Join the Cartly marketplace</p>
    <div class="demo-badges">
      <span class="badge badge-success">admin</span>
      <span class="badge badge-warning">merchant</span>
      <span class="badge">customer</span>
    </div>
    <form method="post" action="<?= BASE_URL ?>/auth/register" class="mt-2">
      <?= Csrf::field() ?>
      <div class="form-grid">
        <div class="form-row"><label>Username</label><input name="username" required></div>
        <div class="form-row"><label>Full name</label><input name="full_name" required></div>
      </div>
      <div class="form-row"><label>Email</label><input name="email" type="email" required></div>
      <div class="form-row"><label>Phone</label><input name="phone" type="tel"></div>
      <div class="form-grid">
        <div class="form-row"><label>Password</label><input name="password" type="password" required></div>
        <div class="form-row"><label>Confirm password</label><input name="confirm" type="password" required></div>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Register</button>
    </form>
    <p class="help">Already have an account? <a href="<?= BASE_URL ?>/auth/login">Login</a></p>
  </div>
</div>
<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>
