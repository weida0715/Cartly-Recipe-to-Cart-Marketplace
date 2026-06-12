<?php use App\Helpers\Csrf; ?>
<h2>Merchant approval</h2>
<?php if (!$pending): ?>
  <div class="card text-muted">No pending stores.</div>
<?php else:
  foreach ($pending as $s): ?>
    <div class="card">
      <h3><?= htmlspecialchars($s['store_name']) ?></h3>
      <p class="text-muted">Owner: <?= htmlspecialchars($s['username']) ?> · <?= htmlspecialchars($s['email']) ?></p>
      <p><?= nl2br(htmlspecialchars($s['store_description'] ?? '')) ?></p>
      <div class="flex">
        <form method="post" action="<?= BASE_URL ?>/admin/merchants/<?= (int) $s['store_id'] ?>/approve">
          <?= Csrf::field() ?>
          <button class="btn btn-primary">Approve</button>
        </form>
        <form method="post" action="<?= BASE_URL ?>/admin/merchants/<?= (int) $s['store_id'] ?>/reject" class="flex">
          <?= Csrf::field() ?>
          <input name="admin_note" placeholder="Reason for rejection">
          <button class="btn btn-danger">Reject</button>
        </form>
        <form method="post" action="<?= BASE_URL ?>/admin/merchants/<?= (int) $s['store_id'] ?>/close" class="flex">
          <?= Csrf::field() ?>
          <input name="admin_note" placeholder="Reason for closure">
          <button class="btn btn-outline">Close store</button>
        </form>
      </div>
    </div>
  <?php endforeach; endif; ?>