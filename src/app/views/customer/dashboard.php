<h2>My dashboard</h2>
<div class="grid grid-2">
  <div class="card">
    <h3>Merchant request</h3>
    <p class="text-muted">Want to open a store? Submit your store details for admin approval.</p>
    <?php if (($storeRequest ?? null)): ?>
      <p><strong>Status:</strong> <span class="badge"><?= htmlspecialchars($storeRequest['store_status']) ?></span></p>
      <p><strong>Store:</strong> <?= htmlspecialchars($storeRequest['store_name']) ?></p>
      <p class="text-muted"><?= htmlspecialchars($storeRequest['admin_note'] ?? '') ?></p>
    <?php else: ?>
      <button type="button" class="btn btn-primary" id="merchantRequestToggle">Request to open store</button>
      <form method="post" action="<?= BASE_URL ?>/merchant/request" class="stack mt-2" id="merchantRequestForm"
        style="display:none;">
        <?= \App\Helpers\Csrf::field() ?>
        <div class="form-row"><label>Store name</label><input name="store_name" required></div>
        <div class="form-row"><label>Contact email</label><input type="email" name="contact_email" required></div>
        <div class="form-row"><label>Contact phone</label><input name="contact_phone" type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="20" required></div>
        <div class="form-row"><label>Store address</label><textarea name="store_address" required></textarea></div>
        <div class="form-row"><label>Description</label><textarea name="store_description"></textarea></div>
        <div class="form-grid">
          <div class="form-row"><label>Opening time</label><input type="time" name="opening_time"></div>
          <div class="form-row"><label>Closing time</label><input type="time" name="closing_time"></div>
        </div>
        <button class="btn btn-primary">Submit request</button>
      </form>
      <script>
        const toggle = document.getElementById('merchantRequestToggle');
        const form = document.getElementById('merchantRequestForm');
        if (toggle && form) {
          toggle.addEventListener('click', () => {
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
          });
        }
      </script>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3>Recent orders</h3>
    <?php if (!$orders): ?>
      <p class="text-muted">No orders yet.</p>
    <?php else: ?>
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td><a href="<?= BASE_URL ?>/orders/<?= (int) $o['order_id'] ?>">#<?= (int) $o['order_id'] ?></a></td>
              <td><?= htmlspecialchars($o['created_at']) ?></td>
              <td>RM <?= number_format((float) $o['total_amount'], 2) ?></td>
              <td><span class="badge"><?= htmlspecialchars($o['order_status']) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3>My recipes</h3>
    <?php if (!$recipes): ?>
      <p class="text-muted">No recipes yet.</p>
    <?php else: ?>
      <ul>
        <?php foreach ($recipes as $r): ?>
          <li><a href="<?= BASE_URL ?>/recipes/<?= (int) $r['recipe_id'] ?>"><?= htmlspecialchars($r['recipe_title']) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <a class="btn btn-primary mt-2" href="<?= BASE_URL ?>/recipes/create">Add new recipe</a>
    <a class="btn btn-outline mt-2" href="<?= BASE_URL ?>/saved-recipes">View saved recipes</a>
  </div>
</div>
