<?php use App\Helpers\Csrf; ?>
<h2>Reports</h2>
<?php if (!$reports): ?>
  <div class="card text-muted">No open reports.</div>
<?php else:
  foreach ($reports as $r): ?>
    <div class="card">
      <div class="flex-between">
        <strong>Report #<?= (int) $r['report_id'] ?> — <?= htmlspecialchars($r['target_type']) ?>
          #<?= (int) $r['target_id'] ?></strong>
        <span class="text-muted"><?= htmlspecialchars($r['created_at']) ?> by <?= htmlspecialchars($r['reporter']) ?></span>
      </div>
      <p><strong>Status:</strong> <?= htmlspecialchars($r['status']) ?></p>
      <p><strong>Reason:</strong> <?= nl2br(htmlspecialchars($r['reason'] ?? '')) ?></p>
      <div class="flex">
        <form method="post" action="<?= BASE_URL ?>/admin/reports/<?= (int) $r['report_id'] ?>/resolve">
          <?= Csrf::field() ?>
          <input type="hidden" name="action" value="resolve">
          <button class="btn btn-primary">Resolve</button>
        </form>
        <form method="post" action="<?= BASE_URL ?>/admin/reports/<?= (int) $r['report_id'] ?>/resolve">
          <?= Csrf::field() ?>
          <input type="hidden" name="action" value="reviewed">
          <button class="btn btn-outline">Mark reviewed</button>
        </form>
        <form method="post" action="<?= BASE_URL ?>/admin/reports/<?= (int) $r['report_id'] ?>/resolve">
          <?= Csrf::field() ?>
          <input type="hidden" name="action" value="hide">
          <button class="btn btn-danger">Hide/remove target</button>
        </form>
      </div>
    </div>
  <?php endforeach; endif; ?>