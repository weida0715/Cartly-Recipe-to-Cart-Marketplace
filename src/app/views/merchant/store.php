<?php
use App\Helpers\Csrf;
use App\Helpers\StoreHours;

$store = is_array($store ?? null) ? $store : [];
$statistics = is_array($statistics ?? null) ? $statistics : [];
$openingValue = substr((string) ($store['opening_time'] ?? '08:00'), 0, 5);
$closingValue = substr((string) ($store['closing_time'] ?? '20:00'), 0, 5);
?>
<div class="store-profile-heading">
  <div>
    <p class="hero-eyebrow">Merchant workspace</p>
    <h2><?= \App\Helpers\Icon::render('store-profile', 'heading-icon') ?>Store profile</h2>
    <p class="text-muted">Keep customer-facing information and operating hours accurate.</p>
  </div>
  <?php if ($store): ?>
    <span class="badge <?= ($store['store_status'] ?? '') === 'approved' ? 'badge-success' : 'badge-warning' ?>">
      <?= htmlspecialchars((string) ($store['store_status'] ?? 'pending')) ?>
    </span>
  <?php endif; ?>
</div>

<?php if ($store && !empty($store['admin_note'])): ?>
  <div class="flash flash-info"><strong>Admin note:</strong> <?= htmlspecialchars((string) $store['admin_note']) ?></div>
<?php endif; ?>

<?php if ($store): ?>
  <section class="store-profile-stats" aria-labelledby="store-statistics-title">
    <div class="section-heading">
      <div>
        <h3 id="store-statistics-title">Store statistics</h3>
        <p>Current performance and catalogue activity.</p>
      </div>
    </div>
    <div class="stat-grid">
      <div class="stat"><div class="num"><?= (int) ($statistics['active_products'] ?? 0) ?></div><div class="label">Active products</div></div>
      <div class="stat"><div class="num"><?= (int) ($statistics['total_orders'] ?? 0) ?></div><div class="label">Orders received</div></div>
      <div class="stat"><div class="num">RM <?= number_format((float) ($statistics['revenue'] ?? 0), 2) ?></div><div class="label">Product revenue</div></div>
      <div class="stat"><div class="num"><?= number_format((float) ($store['rating'] ?? 0), 1) ?></div><div class="label">Store rating</div></div>
    </div>
  </section>
<?php endif; ?>

<form method="post" action="<?= BASE_URL ?>/merchant/store" class="store-profile-form" enctype="multipart/form-data">
  <?= Csrf::field() ?>

  <section class="card store-profile-card" aria-labelledby="store-details-title">
    <div class="store-card-heading">
      <div>
        <h3 id="store-details-title">Store details</h3>
        <p class="text-muted">Information customers see on your store page.</p>
      </div>
    </div>
    <div class="form-row"><label>Store name</label><input name="store_name" value="<?= htmlspecialchars((string) ($store['store_name'] ?? '')) ?>" required></div>
    <div class="form-row store-logo-field">
      <label>Store logo <span class="text-muted">(optional, JPG/PNG/WEBP/GIF, max 2 MB)</span></label>
      <?php if ($store): ?>
        <div class="store-logo-preview">
          <?= \App\Helpers\Icon::storeLogo($store, 'store-profile-logo') ?>
          <span class="text-muted"><?= !empty($store['store_logo']) ? 'Current logo' : 'Initials preview until a logo is uploaded' ?></span>
        </div>
      <?php endif; ?>
      <input type="file" name="store_logo" accept="image/jpeg,image/png,image/webp,image/gif">
    </div>
    <div class="form-row"><label>Description</label><textarea name="store_description"><?= htmlspecialchars((string) ($store['store_description'] ?? '')) ?></textarea></div>
    <div class="form-grid">
      <div class="form-row"><label>Contact email</label><input name="contact_email" type="email" value="<?= htmlspecialchars((string) ($store['contact_email'] ?? '')) ?>" required></div>
      <div class="form-row"><label>Contact phone</label><input name="contact_phone" type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="20" value="<?= htmlspecialchars((string) ($store['contact_phone'] ?? '')) ?>"></div>
    </div>
    <div class="form-row"><label>Address</label><textarea name="store_address" required><?= htmlspecialchars((string) ($store['store_address'] ?? '')) ?></textarea></div>
  </section>

  <section class="card store-profile-card operation-hours-card" aria-labelledby="operation-hours-title">
    <div class="store-card-heading">
      <div>
        <h3 id="operation-hours-title">Operating hours</h3>
        <p class="text-muted">Overnight schedules are supported. Opening and closing times must be different.</p>
      </div>
      <?php if ($store): ?>
        <span class="operation-hours-summary">
          <?= htmlspecialchars(StoreHours::display($store['opening_time'] ?? null)) ?> - <?= htmlspecialchars(StoreHours::display($store['closing_time'] ?? null)) ?>
        </span>
      <?php endif; ?>
    </div>
    <div class="form-grid">
      <div class="form-row"><label for="opening-time">Opening time</label><input id="opening-time" type="time" name="opening_time" value="<?= htmlspecialchars($openingValue) ?>" required></div>
      <div class="form-row"><label for="closing-time">Closing time</label><input id="closing-time" type="time" name="closing_time" value="<?= htmlspecialchars($closingValue) ?>" required></div>
    </div>
  </section>

  <div class="store-profile-actions">
    <button class="btn btn-primary"><?= $store ? 'Save store profile' : 'Submit for approval' ?></button>
  </div>
</form>