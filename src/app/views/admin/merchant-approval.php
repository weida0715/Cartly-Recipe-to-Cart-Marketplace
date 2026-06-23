<?php
use App\Helpers\Csrf;

$pending = is_array($pending ?? null) ? $pending : [];
$approvedHistory = is_array($approvedHistory ?? null) ? $approvedHistory : [];
$formatDate = static function ($value): string {
    $timestamp = strtotime((string) $value);
    return $timestamp === false ? 'Not recorded' : date('d M Y, g:i A', $timestamp);
};
$formatTime = static function ($value): string {
    $timestamp = strtotime((string) $value);
    return $timestamp === false ? 'Not provided' : date('g:i A', $timestamp);
};
?>
<div class="merchant-approval-heading">
  <div>
    <p class="hero-eyebrow">Merchant administration</p>
    <h2>Merchant approval</h2>
    <p class="text-muted">Review complete store requests and keep a record of approved merchants.</p>
  </div>
  <span class="badge badge-warning"><?= count($pending) ?> pending</span>
</div>

<section class="merchant-request-section" aria-labelledby="pending-request-title">
  <div class="section-heading">
    <div>
      <h3 id="pending-request-title">Pending requests</h3>
      <p>Requests are shown oldest first so earlier submissions can be reviewed first.</p>
    </div>
  </div>

  <?php if (!$pending): ?>
    <div class="card text-muted">No pending merchant requests.</div>
  <?php else: ?>
    <div class="merchant-request-list">
      <?php foreach ($pending as $store): ?>
        <article class="card merchant-request-card">
          <header class="merchant-request-header">
            <div>
              <div class="merchant-request-badges">
                <span class="badge badge-warning">pending</span>
                <span class="badge">Requested <?= htmlspecialchars($formatDate($store['created_at'] ?? null)) ?></span>
              </div>
              <h3><?= htmlspecialchars((string) (($store['store_name'] ?? '') ?: 'Unnamed store')) ?></h3>
              <p class="text-muted">Submitted by <?= htmlspecialchars((string) (($store['owner_name'] ?? '') ?: ($store['username'] ?? '') ?: 'Unknown user')) ?></p>
            </div>
          </header>

          <div class="merchant-request-details">
            <div><span>Account</span><strong><?= htmlspecialchars((string) (($store['username'] ?? '') ?: '')) ?></strong><small><?= htmlspecialchars((string) (($store['account_email'] ?? '') ?: '')) ?></small></div>
            <div><span>Store contact</span><strong><?= htmlspecialchars((string) (($store['contact_email'] ?? '') ?: 'Not provided')) ?></strong><small><?= htmlspecialchars((string) (($store['contact_phone'] ?? '') ?: 'Not provided')) ?></small></div>
            <div>
              <span>Operating hours</span>
              <strong>
                <?php if (!empty($store['opening_time']) && !empty($store['closing_time'])): ?>
                  <?= htmlspecialchars($formatTime($store['opening_time'])) ?> - <?= htmlspecialchars($formatTime($store['closing_time'])) ?>
                <?php else: ?>
                  Not provided
                <?php endif; ?>
              </strong>
            </div>
            <div><span>Store address</span><strong><?= nl2br(htmlspecialchars((string) (($store['store_address'] ?? '') ?: 'Not provided'))) ?></strong></div>
          </div>

          <div class="merchant-request-description">
            <span>Description</span>
            <p><?= nl2br(htmlspecialchars((string) (($store['store_description'] ?? '') ?: 'No description provided.'))) ?></p>
          </div>

          <div class="merchant-request-actions">
            <form method="post" action="<?= BASE_URL ?>/admin/merchants/<?= (int) ($store['store_id'] ?? 0) ?>/approve">
              <?= Csrf::field() ?>
              <button class="btn btn-primary">Approve merchant</button>
            </form>
            <form method="post" action="<?= BASE_URL ?>/admin/merchants/<?= (int) ($store['store_id'] ?? 0) ?>/reject" class="merchant-decision-form">
              <?= Csrf::field() ?>
              <label class="sr-only" for="reject-note-<?= (int) ($store['store_id'] ?? 0) ?>">Reason for rejection</label>
              <input id="reject-note-<?= (int) ($store['store_id'] ?? 0) ?>" name="admin_note" maxlength="500" placeholder="Reason for rejection" required>
              <button class="btn btn-danger">Reject</button>
            </form>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<section class="merchant-request-section" aria-labelledby="approved-history-title">
  <div class="section-heading">
    <div>
      <h3 id="approved-history-title">Approved merchant request history</h3>
      <p>Approved requests remain listed after approval, including stores later closed by an administrator.</p>
    </div>
    <span class="badge badge-success"><?= count($approvedHistory) ?> records</span>
  </div>

  <?php if (!$approvedHistory): ?>
    <div class="card text-muted">No approved merchant requests yet.</div>
  <?php else: ?>
    <div class="merchant-history-table-wrap">
      <table class="table merchant-history-table">
        <thead>
          <tr>
            <th>Store</th>
            <th>Owner</th>
            <th>Requested</th>
            <th>Approved</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($approvedHistory as $store): ?>
            <?php $status = (string) ($store['store_status'] ?? 'approved'); ?>
            <tr>
              <td><strong><?= htmlspecialchars((string) (($store['store_name'] ?? '') ?: 'Unnamed store')) ?></strong><small><?= htmlspecialchars((string) (($store['contact_email'] ?? '') ?: '')) ?></small></td>
              <td><?= htmlspecialchars((string) (($store['owner_name'] ?? '') ?: ($store['username'] ?? '') ?: 'Unknown')) ?><small>@<?= htmlspecialchars((string) (($store['username'] ?? '') ?: '')) ?></small></td>
              <td><?= htmlspecialchars($formatDate($store['created_at'] ?? null)) ?></td>
              <td><?= htmlspecialchars($formatDate($store['approved_at'] ?? null)) ?></td>
              <td><span class="badge <?= $status === 'approved' ? 'badge-success' : 'badge-danger' ?>"><?= htmlspecialchars($status) ?></span></td>
              <td>
                <?php if ($status === 'approved'): ?>
                  <form method="post" action="<?= BASE_URL ?>/admin/merchants/<?= (int) ($store['store_id'] ?? 0) ?>/close" class="merchant-history-action">
                    <?= Csrf::field() ?>
                    <label class="sr-only" for="close-note-<?= (int) ($store['store_id'] ?? 0) ?>">Reason for closure</label>
                    <input id="close-note-<?= (int) ($store['store_id'] ?? 0) ?>" name="admin_note" maxlength="500" placeholder="Closure reason" required>
                    <button class="btn btn-outline btn-sm">Close store</button>
                  </form>
                <?php else: ?>
                  <span class="text-muted"><?= htmlspecialchars((string) ($store['admin_note'] ?? 'Closed')) ?></span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>
