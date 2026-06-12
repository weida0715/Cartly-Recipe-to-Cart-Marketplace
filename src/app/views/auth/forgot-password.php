<?php
require_once dirname(__DIR__) . '/layout/header.php';
use App\Helpers\Csrf;
$token = $_GET['token'] ?? ($_SESSION['reset_token'] ?? '');
?>
<div class="auth-shell">
  <div class="auth-card">
    <h1>Forgot password</h1>
    <?php if (!empty($token)): ?>
      <p class="sub">Redirecting you to password reset in 3 seconds...</p>
      <div class="card text-muted">Your reset link is ready.</div>
      <script>
        setTimeout(() => {
          window.location.href = '<?= BASE_URL ?>/auth/reset-password?token=<?= htmlspecialchars($token) ?>';
        }, 3000);
      </script>
    <?php else: ?>
      <p class="sub">Enter your email and we'll send reset instructions.</p>
      <form method="post" action="<?= BASE_URL ?>/auth/forgot-password">
        <?= Csrf::field() ?>
        <div class="form-row"><label>Email</label><input name="email" type="email" required></div>
        <button type="submit" class="btn btn-primary btn-block">Send reset link</button>
      </form>
    <?php endif; ?>
    <p class="help"><a href="<?= BASE_URL ?>/auth/login">Back to login</a></p>
  </div>
</div>
<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>