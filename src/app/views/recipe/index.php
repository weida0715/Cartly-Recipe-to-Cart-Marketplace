<div class="recipe-page-heading">
  <div>
    <h2>Discover recipes</h2>
    <p>Find a meal, adjust the servings, and turn its ingredients into a cart.</p>
  </div>
  <a class="btn btn-accent" href="<?= BASE_URL ?>/recipes/create">+ New recipe</a>
</div>

<div class="recipe-layout">
  <aside class="recipe-filter-column" aria-label="Recipe filters">
    <form class="filters recipe-filters" method="get" action="<?= BASE_URL ?>/recipes" data-search-reset>
      <div class="recipe-filter-heading">
        <h3>Filters</h3>
        <?php if ($q !== '' || ($cuisine ?? '') !== '' || ($difficulty ?? '') !== '' || ($sort ?? 'newest') !== 'newest'): ?>
          <a href="<?= BASE_URL ?>/recipes">Reset</a>
        <?php endif; ?>
      </div>

      <div class="form-row">
        <label for="recipe-search">Search</label>
        <input class="input" id="recipe-search" type="search" name="q" value="<?= htmlspecialchars($q) ?>"
          placeholder="Title or description" autocomplete="off" data-search-reset-input>
      </div>

      <div class="form-row">
        <label for="recipe-cuisine">Cuisine</label>
        <input class="input" id="recipe-cuisine" type="search" name="cuisine"
          value="<?= htmlspecialchars($cuisine ?? '') ?>" placeholder="e.g. Malaysian" autocomplete="off"
          data-search-reset-input>
      </div>

      <div class="form-row">
        <label for="recipe-difficulty">Difficulty</label>
        <select class="input" id="recipe-difficulty" name="difficulty">
          <option value="">All difficulty levels</option>
          <?php foreach (['easy', 'medium', 'hard'] as $difficultyOption): ?>
            <option value="<?= htmlspecialchars($difficultyOption) ?>" <?= ($difficulty ?? '') === $difficultyOption ? 'selected' : '' ?>>
              <?= htmlspecialchars(ucfirst($difficultyOption)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-row">
        <label for="recipe-sort">Sort by</label>
        <select class="input" id="recipe-sort" name="sort">
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
      </div>

      <button class="btn btn-primary btn-block" type="submit">Apply filters</button>
    </form>
  </aside>

  <section class="recipe-results" aria-labelledby="recipe-results-title">
    <div class="recipe-results-heading">
      <h3 id="recipe-results-title">Recipes</h3>
      <span><?= (int) $total ?> <?= (int) $total === 1 ? 'result' : 'results' ?></span>
    </div>

    <?php if (!$recipes): ?>
      <div class="card text-center text-muted">No recipes match your filters.</div>
    <?php else: ?>
      <div class="recipe-grid">
        <?php foreach ($recipes as $r): ?>
          <div class="recipe-card">
            <div class="thumb">
              <?php if (!empty($r['image'])): ?>
                <img src="<?= htmlspecialchars(UPLOAD_URL . '/' . ltrim((string) ($r['image'] ?? ''), '/'), ENT_QUOTES, 'UTF-8') ?>"
                  alt="<?= htmlspecialchars($r['recipe_title'] ?? '') ?>">
              <?php else: ?>
                <span class="thumb-fallback">Recipe</span>
              <?php endif; ?>
            </div>
            <div class="body">
              <h3><?= htmlspecialchars($r['recipe_title'] ?? '') ?></h3>
              <div class="text-muted">by <?= htmlspecialchars($r['username'] ?? 'Unknown') ?> &middot; <?= htmlspecialchars($r['cuisine_type'] ?? 'Other') ?>
              </div>
              <div class="text-muted"><?= htmlspecialchars($r['difficulty'] ?? 'easy') ?> &middot;
                <?= (int) ($r['prep_time'] ?? 0) + (int) ($r['cook_time'] ?? 0) ?> min
              </div>
              <a class="btn btn-primary btn-sm mt-1" href="<?= BASE_URL ?>/recipes/<?= (int) ($r['recipe_id'] ?? 0) ?>">Open</a>
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
      <div class="pagination recipe-pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <?php $query['page'] = $i; ?>
          <a class="btn <?= $i === (int) $page ? 'btn-primary' : 'btn-outline' ?> btn-sm"
            href="<?= BASE_URL ?>/recipes?<?= htmlspecialchars(http_build_query($query)) ?>"><?= $i ?></a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </section>
</div>
