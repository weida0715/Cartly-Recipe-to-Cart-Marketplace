<?php
use App\Helpers\Csrf;

$today = new DateTimeImmutable('today');
$tomorrow = $today->modify('+1 day');
$totalVouchers = count($vouchers);
$activeVouchers = 0;
foreach ($vouchers as $voucher) {
    if (($voucher['status'] ?? '') === 'active') {
        $activeVouchers++;
    }
}
?>
<h2>Vouchers</h2>

<div class="card voucher-admin-hero">
  <div>
    <p class="hero-eyebrow">Merchant voucher desk</p>
    <h3>Keep promotions clean</h3>
    <p class="text-muted">Create, edit, and deactivate store vouchers without leaving the page.</p>
  </div>
  <div class="voucher-admin-stats">
    <span class="badge badge-success"><?= (int) $activeVouchers ?> active</span>
    <span class="badge"><?= (int) $totalVouchers ?> total</span>
  </div>
</div>

<div class="card">
  <h3>Create voucher</h3>
  <form method="post" action="<?= BASE_URL ?>/merchant/vouchers" class="voucher-edit-form" data-voucher-expiry-form>
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
      <div class="form-row"><label>Min spend</label><input type="number" step="0.01" min="0" name="minimum_spend" value="0"></div>
    </div>
    <div class="voucher-toggle-row">
      <label class="voucher-toggle">
        <input type="checkbox" name="no_expiry" value="1" data-voucher-no-expiry>
        <span>No expiry date</span>
      </label>
    </div>
    <div class="voucher-date-range-shell" data-voucher-expiry-shell>
      <div class="form-grid">
        <div class="form-row"><label>Start date</label><input type="date" name="start_date" value="<?= htmlspecialchars($today->format('Y-m-d')) ?>" required></div>
        <div class="form-row"><label>End date</label><input type="date" name="end_date" value="<?= htmlspecialchars($tomorrow->format('Y-m-d')) ?>" required></div>
      </div>
    </div>
    <div class="form-grid">
      <div class="form-row"><label>Usage limit (0 = unlimited)</label><input type="number" min="0" name="usage_limit" value="0"></div>
    </div>
    <div class="voucher-record-actions">
      <button class="btn btn-primary">Create voucher</button>
    </div>
  </form>
</div>

<?php if (!$vouchers): ?>
  <div class="card text-center text-muted">No vouchers yet.</div>
<?php else: ?>
  <div class="voucher-records">
    <?php foreach ($vouchers as $v): ?>
      <?php
        $noExpiry = empty($v['start_date']) && empty($v['end_date']);
        $status = (string) $v['status'];
        $statusClass = match ($status) {
            'active' => 'badge-success',
            'inactive' => 'badge-danger',
            default => 'badge-warning',
        };
        $discountLabel = $v['discount_type'] === 'percentage'
            ? number_format((float) $v['discount_value'], 0) . '% off'
            : 'RM ' . number_format((float) $v['discount_value'], 2) . ' off';
        $validityLabel = $noExpiry
            ? 'No expiry date'
            : trim(($v['start_date'] ? $v['start_date'] : 'Any time') . ' to ' . ($v['end_date'] ? $v['end_date'] : 'No end date'));
        $dialogId = 'voucher-edit-' . (int) $v['voucher_id'];
      ?>
      <article class="card voucher-record">
        <div class="voucher-record-top">
          <div class="voucher-record-title">
            <div class="voucher-chip-row">
              <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($status) ?></span>
              <span class="badge"><?= htmlspecialchars($v['discount_type']) ?></span>
            </div>
            <h3><?= htmlspecialchars($v['voucher_code']) ?></h3>
            <p class="text-muted">Merchant voucher for this store</p>
          </div>
          <div class="voucher-record-kpi">
            <strong><?= htmlspecialchars($discountLabel) ?></strong>
            <span>discount</span>
          </div>
        </div>

        <div class="voucher-record-meta">
          <div>
            <span>Minimum spend</span>
            <strong>RM <?= number_format((float) $v['minimum_spend'], 2) ?></strong>
          </div>
          <div>
            <span>Usage</span>
            <strong>Used <?= (int) $v['used_count'] ?>/<?= (int) $v['usage_limit'] ?: '∞' ?></strong>
          </div>
          <div>
            <span>Validity</span>
            <strong><?= htmlspecialchars($validityLabel) ?></strong>
          </div>
        </div>

        <div class="voucher-record-actions">
          <button type="button" class="btn btn-outline btn-sm" data-category-edit-open data-dialog-target="<?= htmlspecialchars($dialogId) ?>">Edit</button>
          <form method="post" action="<?= BASE_URL ?>/merchant/vouchers/<?= (int) $v['voucher_id'] ?>/delete"
            data-confirm="Deactivate this voucher?">
            <?= Csrf::field() ?>
            <button class="btn btn-danger btn-sm">Deactivate</button>
          </form>
        </div>
      </article>

      <dialog class="category-dialog voucher-dialog" id="<?= htmlspecialchars($dialogId) ?>" data-category-edit-dialog>
        <form method="post" action="<?= BASE_URL ?>/merchant/vouchers/<?= (int) $v['voucher_id'] ?>/update"
          class="voucher-edit-form" data-voucher-expiry-form>
          <?= Csrf::field() ?>
          <div class="dialog-header">
            <h3>Edit voucher</h3>
            <button type="button" class="btn btn-ghost btn-sm" data-dialog-close aria-label="Close">Close</button>
          </div>
          <div class="form-grid">
            <div class="form-row"><label>Code</label><input name="voucher_code" value="<?= htmlspecialchars($v['voucher_code']) ?>" required></div>
            <div class="form-row">
              <label>Type</label>
              <select name="discount_type">
                <?php foreach (['fixed', 'percentage'] as $type): ?>
                  <option value="<?= $type ?>" <?= $v['discount_type'] === $type ? 'selected' : '' ?>><?= $type ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-grid">
            <div class="form-row"><label>Value</label><input type="number" step="0.01" min="0.01" name="discount_value" value="<?= htmlspecialchars((string) $v['discount_value']) ?>" required></div>
            <div class="form-row"><label>Min spend</label><input type="number" step="0.01" min="0" name="minimum_spend" value="<?= htmlspecialchars((string) $v['minimum_spend']) ?>"></div>
          </div>
          <div class="voucher-toggle-row">
            <label class="voucher-toggle">
              <input type="checkbox" name="no_expiry" value="1" data-voucher-no-expiry <?= $noExpiry ? 'checked' : '' ?>>
              <span>No expiry date</span>
            </label>
            <p class="text-muted">If unchecked, both dates are required.</p>
          </div>
          <div class="voucher-date-range-shell <?= $noExpiry ? 'is-muted' : '' ?>" data-voucher-expiry-shell>
            <div class="form-grid">
              <div class="form-row"><label>Start date</label><input type="date" name="start_date" value="<?= htmlspecialchars((string) ($v['start_date'] ?? '')) ?>" <?= $noExpiry ? 'disabled' : 'required' ?>></div>
              <div class="form-row"><label>End date</label><input type="date" name="end_date" value="<?= htmlspecialchars((string) ($v['end_date'] ?? '')) ?>" <?= $noExpiry ? 'disabled' : 'required' ?>></div>
            </div>
          </div>
          <div class="form-grid">
            <div class="form-row"><label>Usage limit (0 = unlimited)</label><input type="number" min="0" name="usage_limit" value="<?= (int) $v['usage_limit'] ?>"></div>
          </div>
          <div class="dialog-actions">
            <button type="button" class="btn btn-outline btn-sm" data-dialog-close>Cancel</button>
            <button class="btn btn-primary btn-sm">Save changes</button>
          </div>
        </form>
      </dialog>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<script>
(() => {
  const forms = document.querySelectorAll('[data-voucher-expiry-form]');
  forms.forEach((form) => {
    const toggle = form.querySelector('[data-voucher-no-expiry]');
    const shell = form.querySelector('[data-voucher-expiry-shell]');
    if (!toggle || !shell) return;

    const sync = () => {
      const locked = toggle.checked;
      shell.classList.toggle('is-muted', locked);
      shell.querySelectorAll('input[type="date"]').forEach((input) => {
        input.disabled = locked;
        input.required = !locked;
      });
    };

    toggle.addEventListener('change', sync);
    sync();
  });
})();
</script>
