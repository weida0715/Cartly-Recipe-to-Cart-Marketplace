<?php use App\Helpers\Csrf; ?>
<h2>Users</h2>
<table class="table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Email</th>
      <th>Role</th>
      <th>Status</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= (int) $u['user_id'] ?></td>
        <td><?= htmlspecialchars($u['username']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td>
          <form method="post" action="<?= BASE_URL ?>/admin/users/<?= (int) $u['user_id'] ?>/role" class="flex">
            <?= Csrf::field() ?>
            <select name="role">
              <?php foreach (['customer', 'merchant', 'admin'] as $role): ?>
                <option value="<?= $role ?>" <?= $u['role'] === $role ? 'selected' : '' ?>><?= $role ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-outline btn-sm">Role</button>
          </form>
        </td>
        <td><?= htmlspecialchars($u['status']) ?></td>
        <td>
          <form method="post" action="<?= BASE_URL ?>/admin/users/<?= (int) $u['user_id'] ?>/status" class="flex">
            <?= Csrf::field() ?>
            <select name="status">
              <?php foreach (['active', 'inactive', 'deactivated'] as $s): ?>
                <option <?= $u['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-outline btn-sm">Update</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>