<?php
require_once dirname(__DIR__) . '/layout/header.php';
use App\Helpers\Csrf;
$autoToken = $token ?? '';
?>
<div class="auth-shell">
    <div class="auth-card">
        <h1>Reset password</h1>
        <p class="sub">Choose a new password for your Cartly account.</p>
        <form method="post" action="<?= BASE_URL ?>/auth/reset-password">
            <?= Csrf::field() ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($autoToken) ?>">
            <div class="form-row">
                <label for="new-password">New password</label>
                <input id="new-password" name="password" type="password" minlength="6" autocomplete="new-password"
                    required>
            </div>
            <div class="form-row">
                <label for="confirm-password">Confirm password</label>
                <input id="confirm-password" name="confirm" type="password" minlength="6" autocomplete="new-password"
                    required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Reset password</button>
        </form>
        <p class="help"><a href="<?= BASE_URL ?>/auth/login">Back to login</a></p>
    </div>
</div>
<?php require_once dirname(__DIR__) . '/layout/footer.php'; ?>
