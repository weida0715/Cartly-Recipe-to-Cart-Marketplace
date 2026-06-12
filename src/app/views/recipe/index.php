<h2>Discover recipes</h2>
<form class="filters" method="get" action="<?= BASE_URL ?>/recipes">
  <input class="input" type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search by name, cuisine…">
  <input class="input" type="text" name="cuisine" value="<?= htmlspecialchars($cuisine ?? '') ?>" placeholder="Cuisine">
  <select class="input" name="difficulty">
    <option value="">All difficulty</option>
    <?php foreach (['easy', 'medium', 'hard'] as $difficultyOption): ?>
      <option value="<?= htmlspecialchars($difficultyOption) ?>" <?= ($difficulty ?? '') === $difficultyOption ? 'selected' : '' ?>>
        <?= htmlspecialchars(ucfirst($difficultyOption)) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <select class="input" name="sort">
    <?php foreach ([
      'newest' => 'Newest',
      'oldest' => 'Oldest',
      'title_asc' => 'Title: A to Z',
      'title_desc' => 'Title: Z to A',
      'prep_asc' => 'Prep time: Low to High',
      'prep_desc' => 'Prep time: High to Low',
      'review_desc' => 'Reviews: Highest Rated',
      'review_count_desc' => 'Reviews: Most Reviewed',
    ] as $value => $label): ?>
      <option value="<?= htmlspecialchars($value) ?>" <?= ($sort ?? 'newest') === $value ? 'selected' : '' ?>>
        <?= htmlspecialchars($label) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <button class="btn btn-primary">Search</button>
  <a class="btn btn-accent" href="<?= BASE_URL ?>/recipes/create">+ New recipe</a>
</form>

<?php if (!$recipes): ?>
  <div class="card text-center text-muted">No recipes match.</div>
<?php else: ?>
  <div class="recipe-grid">
    <?php foreach ($recipes as $r): ?>
      <div class="recipe-card">
        <div class="thumb">🍲</div>
        <div class="body">
          <h3><?= htmlspecialchars($r['recipe_title']) ?></h3>
          <div class="text-muted">by <?= htmlspecialchars($r['username']) ?> · <?= htmlspecialchars($r['cuisine_type']) ?>
          </div>
          <div class="text-muted"><?= htmlspecialchars($r['difficulty']) ?> ·
            <?= (int) $r['prep_time'] + (int) $r['cook_time'] ?> min
          </div>
          <a class="btn btn-primary btn-sm mt-1" href="<?= BASE_URL ?>/recipes/<?= (int) $r['recipe_id'] ?>">Open</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if (!empty($pages) && $pages > 1): ?>
  <?php
  $query = $_GET;
  unset($query['page']);
  ?>
  <div class="pagination mt-3" style="display:flex;gap:.5rem;flex-wrap:wrap;justify-content:center;">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <?php $query['page'] = $i; ?>
      <a class="btn <?= $i === (int) $page ? 'btn-primary' : 'btn-outline' ?> btn-sm"
        href="<?= BASE_URL ?>/recipes?<?= htmlspecialchars(http_build_query($query)) ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
<?php endif; ?>