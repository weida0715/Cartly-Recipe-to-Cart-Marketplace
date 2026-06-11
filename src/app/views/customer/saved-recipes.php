<h2>Saved recipes</h2>
<?php if (!$recipes): ?>
    <div class="card text-muted">No saved recipes yet.</div>
<?php else: ?>
    <div class="grid grid-3">
        <?php foreach ($recipes as $r): ?>
            <div class="card">
                <h3><?= htmlspecialchars($r['recipe_title']) ?></h3>
                <p class="text-muted"><?= htmlspecialchars($r['cuisine_type'] ?? '') ?> ·
                    <?= htmlspecialchars($r['difficulty'] ?? '') ?></p>
                <p><?= htmlspecialchars(substr((string) ($r['description'] ?? ''), 0, 120)) ?></p>
                <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/recipes/<?= (int) $r['recipe_id'] ?>">Open</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>