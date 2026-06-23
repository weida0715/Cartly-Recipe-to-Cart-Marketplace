<?php
use App\Helpers\Csrf;
$user = is_array($user ?? null) ? $user : [];
?>
<div class="profile-header">
  <div>
    <h2><?= \App\Helpers\Icon::render('profile', 'heading-icon') ?>Edit profile</h2>
    <p class="text-muted">Update your account information and password.</p>
  </div>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/profile">Back to profile</a>
</div>

<form method="post" action="<?= BASE_URL ?>/profile" class="card profile-edit-form">
    <?= Csrf::field() ?>
    <div class="form-grid">
        <div class="form-row"><label>Username</label><input name="username"
                value="<?= htmlspecialchars($user['username'] ?? '') ?>" required></div>
        <div class="form-row"><label>Full name</label><input name="full_name"
                value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required></div>
    </div>
    <div class="form-grid">
        <div class="form-row"><label>Email</label><input type="email" name="email"
                value="<?= htmlspecialchars($user['email'] ?? '') ?>" required></div>
        <div class="form-row"><label>Phone</label><input name="phone" type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="20"
                value="<?= htmlspecialchars($user['phone'] ?? '') ?>"></div>
    </div>
    <h3>Change password</h3>
    <p class="text-muted">Leave blank to keep your current password.</p>
    <div class="form-grid">
        <div class="form-row"><label>New password</label><input type="password" name="password" minlength="6"></div>
        <div class="form-row"><label>Confirm password</label><input type="password" name="confirm" minlength="6"></div>
    </div>
    <button class="btn btn-primary">Save profile</button>
</form>
