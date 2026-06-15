<?php
require_once dirname(__DIR__) . '/layout/header.php';
use App\Helpers\Csrf;
$emailSent = $emailSent ?? false;
$resetUrl = $resetUrl ?? null;
$expiresAt = $expiresAt ?? null;
?>
<div class="auth-shell">
  <div class="auth-card">
    <h1>Forgot password</h1>
    <?php if ($emailSent): ?>
      <p class="sub">If an active account exists for that email, reset instructions are ready.</p>
      <?php if (!empty($resetUrl)): ?>
        <div class="reset-link-box">
          <p class="text-muted">For local XAMPP testing, use this reset link:</p>
          <a class="btn btn-primary btn-block" href="<?= htmlspecialchars($resetUrl) ?>">Reset password now</a>
          <?php if (!empty($expiresAt)): ?>
            <p class="help">This link expires at <?= htmlspecialchars($expiresAt) ?>.</p>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <p class="sub">Enter your email and we'll send reset instructions.</p>
      <form method="post" action="<?= BASE_URL ?>/auth/forgot-password">
        <?= Csrf::field() ?>
        <div class="form-row">
          <label for="reset-email">Email</label>
          <input id="reset-email" name="email" type="email" autocomplete="email" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send reset link</button>
      </form>
    <?php endif; ?>
    <p class="help"><a href="<?= BASE_URL ?>/auth/login">Back to login</a></p>
  </div>
</div>
<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>
