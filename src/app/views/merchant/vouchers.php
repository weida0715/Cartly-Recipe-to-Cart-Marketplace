<?php use App\Helpers\Csrf; ?>
<h2>Vouchers</h2>
<div class="card">
  <h3>Create voucher</h3>
  <form method="post" action="<?= BASE_URL ?>/merchant/vouchers">
    <?= Csrf::field() ?>
    <div class="form-grid">
      <div class="form-row"><label>Code</label><input name="voucher_code" maxlength="50" required></div>
      <div class="form-row">
        <label>Type</label>
        <select name="discount_type">
          <option value="fixed">fixed</option>
          <option value="percentage">percentage</option>
        </select>
      </div>
    </div>
    <div class="form-grid">
      <div class="form-row"><label>Value</label><input type="number" step="0.01" min="0.01" name="discount_value" required></div>
      <div class="form-row"><label>Min spend</label><input type="number" step="0.01" min="0" name="minimum_spend" value="0">
      </div>
    </div>
    <div class="form-grid">
      <div class="form-row"><label>Start</label><input type="date" name="start_date"></div>
      <div class="form-row"><label>End</label><input type="date" name="end_date"></div>
      <div class="form-row"><label>Usage limit (0 = unlimited)</label><input type="number" min="0" name="usage_limit" value="0">
      </div>
    </div>
    <button class="btn btn-primary">Create</button>
  </form>
</div>

<table class="table">
  <thead>
    <tr>
      <th>Code</th>
      <th>Type</th>
      <th>Value</th>
      <th>Used</th>
      <th>Status</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($vouchers as $v): ?>
      <tr>
        <td colspan="5">
          <form method="post" action="<?= BASE_URL ?>/merchant/vouchers/<?= (int) $v['voucher_id'] ?>/update"
            class="form-grid">
            <?= Csrf::field() ?>
            <input name="voucher_code" value="<?= htmlspecialchars($v['voucher_code']) ?>" required>
            <select name="discount_type">
              <?php foreach (['fixed', 'percentage'] as $type): ?>
                <option <?= $v['discount_type'] === $type ? 'selected' : '' ?>><?= $type ?></option>
              <?php endforeach; ?>
            </select>
            <input type="number" step="0.01" name="discount_value"
              value="<?= htmlspecialchars((string) $v['discount_value']) ?>">
            <input type="number" step="0.01" name="minimum_spend"
              value="<?= htmlspecialchars((string) $v['minimum_spend']) ?>">
            <input type="date" name="start_date" value="<?= htmlspecialchars((string) $v['start_date']) ?>">
            <input type="date" name="end_date" value="<?= htmlspecialchars((string) $v['end_date']) ?>">
            <input type="number" name="usage_limit" value="<?= (int) $v['usage_limit'] ?>">
            <select name="status">
              <?php foreach (['active', 'inactive', 'expired'] as $status): ?>
                <option <?= $v['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-outline btn-sm">Save</button>
          </form>
          <span class="text-muted">Used: <?= (int) $v['used_count'] ?>/<?= (int) $v['usage_limit'] ?: '∞' ?></span>
        </td>
        <td>
          <form method="post" action="<?= BASE_URL ?>/merchant/vouchers/<?= (int) $v['voucher_id'] ?>/delete"
            data-confirm="Deactivate this voucher?">
            <?= Csrf::field() ?>
            <button class="btn btn-danger btn-sm">Deactivate</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>