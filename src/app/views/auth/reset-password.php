<?php
require_once dirname(__DIR__) . '/layout/header.php';
use App\Helpers\Csrf;
$autoToken = $token ?? ($_SESSION['reset_token'] ?? '');
?>
<div class="auth-shell">
    <div class="auth-card">
        <h1>Reset password</h1>
        <p class="sub">Choose a new password for your Cartly account.</p>
        <form method="post" action="<?= BASE_URL ?>/auth/reset-password">
            <?= Csrf::field() ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($autoToken) ?>">
            <div class="form-row"><label>New password</label><input name="password" type="password" minlength="6"
                    required></div>
            <div class="form-row"><label>Confirm password</label><input name="confirm" type="password" minlength="6"
                    required></div>
            <button type="submit" class="btn btn-primary btn-block">Reset password</button>
        </form>
        <p class="help"><a href="<?= BASE_URL ?>/auth/login">Back to login</a></p>
    </div>
</div>
<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>