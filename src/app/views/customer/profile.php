<?php
$user = is_array($user ?? null) ? $user : [];
$orders = is_array($orders ?? null) ? $orders : [];
$stats = is_array($stats ?? null) ? $stats : [];
$storeRequest = is_array($storeRequest ?? null) ? $storeRequest : null;

$formatDate = static function (?string $value): string {
    if (!$value) {
        return 'Not available';
    }
    $timestamp = strtotime($value);
    return $timestamp ? date('M j, Y', $timestamp) : $value;
};
$statusLabel = static fn(string $status): string => ucwords(str_replace('_', ' ', $status));
$statusClass = static function (string $status): string {
    return match ($status) {
        'completed', 'delivered', 'paid', 'approved' => 'badge-success',
        'cancelled', 'failed', 'rejected' => 'badge-danger',
        'pending', 'processing', 'accepted', 'preparing', 'ready_to_deliver', 'out_for_delivery' => 'badge-warning',
        default => '',
    };
};
?>
<div class="profile-header">
  <div>
    <h2><?= \App\Helpers\Icon::render('profile', 'heading-icon') ?>My profile</h2>
    <p class="text-muted">Manage account details, shortcuts, and recent order activity.</p>
  </div>
  <a class="btn btn-primary" href="<?= BASE_URL ?>/profile/edit">
    <?= \App\Helpers\Icon::render('profile', 'button-icon') ?>Edit profile
  </a>
</div>

<div class="profile-summary-grid">
  <section class="card profile-account-card">
    <div class="profile-avatar" aria-hidden="true">
      <?= htmlspecialchars(mb_strtoupper(mb_substr((string) ($user['username'] ?? 'U'), 0, 1, 'UTF-8'), 'UTF-8')) ?>
    </div>
    <div class="profile-account-details">
      <h3><?= htmlspecialchars($user['full_name'] ?? 'Cartly user') ?></h3>
      <dl class="profile-detail-list">
        <div>
          <dt>Username</dt>
          <dd><?= htmlspecialchars($user['username'] ?? '-') ?></dd>
        </div>
        <div>
          <dt>Email</dt>
          <dd><?= htmlspecialchars($user['email'] ?? '-') ?></dd>
        </div>
        <div>
          <dt>Role</dt>
          <dd><span class="badge"><?= htmlspecialchars($statusLabel((string) ($user['role'] ?? 'customer'))) ?></span></dd>
        </div>
        <div>
          <dt>Joined</dt>
          <dd><?= htmlspecialchars($formatDate($user['created_at'] ?? null)) ?></dd>
        </div>
      </dl>
    </div>
  </section>

  <section class="card">
    <h3><?= \App\Helpers\Icon::render('dashboard', 'heading-icon') ?>Quick stats</h3>
    <div class="profile-stats">
      <div>
        <strong><?= (int) ($stats['orders'] ?? 0) ?></strong>
        <span>Orders</span>
      </div>
      <div>
        <strong><?= (int) ($stats['savedRecipes'] ?? 0) ?></strong>
        <span>Saved recipes</span>
      </div>
      <div>
        <strong><?= (int) ($stats['reviews'] ?? 0) ?></strong>
        <span>Reviews</span>
      </div>
    </div>
    <a class="btn btn-outline btn-block mt-2" href="<?= BASE_URL ?>/orders">
      <?= \App\Helpers\Icon::render('orders', 'button-icon') ?>View order history
    </a>
  </section>

  <section class="card">
    <h3><?= \App\Helpers\Icon::render('saved', 'heading-icon') ?>Quick actions</h3>
    <div class="quick-action-list">
      <a href="<?= BASE_URL ?>/saved-recipes"><?= \App\Helpers\Icon::render('saved') ?>Saved recipes</a>
      <a href="<?= BASE_URL ?>/cart"><?= \App\Helpers\Icon::render('cart') ?>Cart and checkout</a>
      <a href="<?= BASE_URL ?>/recipes/create"><?= \App\Helpers\Icon::render('recipes') ?>Create recipe</a>
    </div>
    <div class="merchant-action">
      <?php if ($storeRequest): ?>
        <span class="badge <?= htmlspecialchars($statusClass((string) ($storeRequest['store_status'] ?? 'pending'))) ?>">
          <?= htmlspecialchars($statusLabel((string) ($storeRequest['store_status'] ?? 'pending'))) ?>
        </span>
        <strong><?= htmlspecialchars($storeRequest['store_name'] ?? 'Merchant request') ?></strong>
        <p class="text-muted">Your merchant application is linked to this account.</p>
      <?php elseif (($user['role'] ?? 'customer') === 'customer'): ?>
        <strong>Want to sell on Cartly?</strong>
        <p class="text-muted">Start a merchant application from your dashboard.</p>
        <a class="btn btn-accent btn-block" href="<?= BASE_URL ?>/dashboard">
          <?= \App\Helpers\Icon::render('merchant', 'button-icon') ?>Apply as merchant
        </a>
      <?php else: ?>
        <strong>Merchant tools</strong>
        <p class="text-muted">Manage your store and products from the merchant area.</p>
        <a class="btn btn-accent btn-block" href="<?= BASE_URL ?>/merchant">
          <?= \App\Helpers\Icon::render('merchant', 'button-icon') ?>Open merchant dashboard
        </a>
      <?php endif; ?>
    </div>
  </section>
</div>

<section class="card profile-orders">
  <div class="flex-between">
    <h3><?= \App\Helpers\Icon::render('orders', 'heading-icon') ?>Recent orders</h3>
    <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/orders">All orders</a>
  </div>
  <?php if (!$orders): ?>
    <p class="text-muted text-center">No recent orders yet.</p>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>Order</th>
          <th>Date</th>
          <th>Status</th>
          <th>Total</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <?php $orderStatus = (string) ($order['display_order_status'] ?? $order['order_status'] ?? 'pending'); ?>
          <tr>
            <td>#<?= (int) ($order['order_id'] ?? 0) ?></td>
            <td><?= htmlspecialchars($formatDate($order['created_at'] ?? null)) ?></td>
            <td>
              <span class="badge <?= htmlspecialchars($statusClass($orderStatus)) ?>">
                <?= htmlspecialchars($statusLabel($orderStatus)) ?>
              </span>
            </td>
            <td>RM <?= number_format((float) ($order['total_amount'] ?? 0), 2) ?></td>
            <td><a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/orders/<?= (int) ($order['order_id'] ?? 0) ?>">View</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>
