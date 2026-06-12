<?php use App\Helpers\Csrf; ?>
<h2>Store profile</h2>
<?php if ($store): ?>
  <p>Status: <span class="badge"><?= htmlspecialchars($store['store_status']) ?></span>
  <?php if (!empty($store['admin_note'])): ?>· Admin note: <?= htmlspecialchars($store['admin_note']) ?><?php endif; ?></p>
<?php endif; ?>
<form method="post" action="<?= BASE_URL ?>/merchant/store" class="card">
  <?= Csrf::field() ?>
  <div class="form-row"><label>Store name</label><input name="store_name" value="<?= htmlspecialchars($store['store_name'] ?? '') ?>" required></div>
  <div class="form-row"><label>Description</label><textarea name="store_description"><?= htmlspecialchars($store['store_description'] ?? '') ?></textarea></div>
  <div class="form-grid">
    <div class="form-row"><label>Contact email</label><input name="contact_email" value="<?= htmlspecialchars($store['contact_email'] ?? '') ?>"></div>
    <div class="form-row"><label>Contact phone</label><input name="contact_phone" value="<?= htmlspecialchars($store['contact_phone'] ?? '') ?>"></div>
  </div>
  <div class="form-row"><label>Address</label><textarea name="store_address"><?= htmlspecialchars($store['store_address'] ?? '') ?></textarea></div>
  <div class="form-grid">
    <div class="form-row"><label>Opens</label><input type="time" name="opening_time" value="<?= htmlspecialchars($store['opening_time'] ?? '08:00') ?>"></div>
    <div class="form-row"><label>Closes</label><input type="time" name="closing_time" value="<?= htmlspecialchars($store['closing_time'] ?? '20:00') ?>"></div>
  </div>
  <button class="btn btn-primary"><?= $store ? 'Save changes' : 'Submit for approval' ?></button>
</form>
