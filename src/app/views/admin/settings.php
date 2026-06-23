<?php use App\Helpers\Csrf; ?>
<div class="settings-heading">
  <div>
    <p class="hero-eyebrow">Platform configuration</p>
    <h2><?= \App\Helpers\Icon::render('settings', 'heading-icon') ?>Delivery settings</h2>
    <p class="text-muted">Set the delivery fee charged once for each merchant included in an order.</p>
  </div>
</div>

<section class="card admin-setting-card" aria-labelledby="delivery-fee-title">
  <div class="admin-setting-copy">
    <h3 id="delivery-fee-title">Delivery fee per merchant</h3>
    <p class="text-muted">A cart containing items from two merchants will receive this fee twice. Existing orders keep the fee saved when they were placed.</p>
  </div>
  <form method="post" action="<?= BASE_URL ?>/admin/settings" class="admin-setting-form">
    <?= Csrf::field() ?>
    <div class="form-row">
      <label for="delivery-fee">Fee amount (RM)</label>
      <input id="delivery-fee" name="delivery_fee" type="number" min="0" max="999.99" step="0.01"
        value="<?= htmlspecialchars(number_format((float) ($deliveryFee ?? 0), 2, '.', '')) ?>" required>
    </div>
    <button class="btn btn-primary">Save delivery fee</button>
  </form>
</section>