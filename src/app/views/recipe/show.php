<?php
use App\Helpers\Csrf;
use App\Helpers\AuthHelper;

$recipe = is_array($recipe ?? null) ? $recipe : [];
$ingredients = is_array($ingredients ?? null) ? $ingredients : [];
$reviews = is_array($reviews ?? null) ? $reviews : [];
$baseServings = max(1, (int) ($recipe['base_servings'] ?? 1));
$instructionText = trim((string) ($recipe['instructions'] ?? ''));
$instructionSteps = [];
if ($instructionText !== '') {
    $parts = preg_split('/(?:\r?\n)+|(?=\d+[.)]\s+)/', $instructionText) ?: [];
    foreach ($parts as $part) {
        $step = trim((string) preg_replace('/^\d+[.)]\s*/', '', trim($part)));
        if ($step !== '') {
            $instructionSteps[] = $step;
        }
    }
}
?>

<section class="recipe-detail-hero">
  <div class="recipe-detail-media">
    <?php if (!empty($recipe['image'])): ?>
      <img src="<?= htmlspecialchars(UPLOAD_URL . '/' . ltrim((string) $recipe['image'], '/'), ENT_QUOTES, 'UTF-8') ?>"
        alt="<?= htmlspecialchars($recipe['recipe_title'] ?? '') ?>">
    <?php else: ?>
      <span class="thumb-fallback">Recipe</span>
    <?php endif; ?>
  </div>

  <div class="recipe-detail-summary">
    <p class="hero-eyebrow">Recipe details</p>
    <h1><?= htmlspecialchars($recipe['recipe_title'] ?? 'Untitled recipe') ?></h1>
    <div class="recipe-detail-meta" aria-label="Recipe summary">
      <span><?= htmlspecialchars($recipe['cuisine_type'] ?? 'Other cuisine') ?></span>
      <span><?= htmlspecialchars(ucfirst((string) ($recipe['difficulty'] ?? 'easy'))) ?></span>
      <span><?= (int) ($recipe['prep_time'] ?? 0) + (int) ($recipe['cook_time'] ?? 0) ?> min</span>
      <span>Serves <?= $baseServings ?></span>
    </div>
    <?php if (trim((string) ($recipe['description'] ?? '')) !== ''): ?>
      <p class="recipe-detail-description"><?= nl2br(htmlspecialchars((string) $recipe['description'])) ?></p>
    <?php endif; ?>

    <div class="recipe-detail-actions">
      <?php if (AuthHelper::check()): ?>
        <form method="post" action="<?= BASE_URL ?>/recipes/<?= (int) ($recipe['recipe_id'] ?? 0) ?>/preview-cart">
          <?= Csrf::field() ?>
          <input type="hidden" name="servings" value="<?= $baseServings ?>" data-recipe-servings-target>
          <button class="btn btn-primary" type="submit"><?= \App\Helpers\Icon::render('cart', 'button-icon') ?>Preview cart</button>
        </form>
        <form method="post" action="<?= BASE_URL ?>/recipes/<?= (int) ($recipe['recipe_id'] ?? 0) ?>/save">
          <?= Csrf::field() ?>
          <button class="btn btn-outline" type="submit"><?= !empty($isSaved) ? 'Unsave recipe' : 'Save recipe' ?></button>
        </form>
        <?php if ((int) ($recipe['user_id'] ?? 0) === (int) AuthHelper::id()): ?>
          <form method="post" action="<?= BASE_URL ?>/recipes/<?= (int) ($recipe['recipe_id'] ?? 0) ?>/hide">
            <?= Csrf::field() ?>
            <button class="btn btn-outline" type="submit">Hide recipe</button>
          </form>
        <?php endif; ?>
      <?php else: ?>
        <a class="btn btn-primary" href="<?= BASE_URL ?>/auth/login">Login to generate cart</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<div class="recipe-detail-content">
  <section class="card recipe-ingredients-card" aria-labelledby="recipe-ingredients-title">
    <div class="recipe-section-heading">
      <div>
        <p class="hero-eyebrow">What you need</p>
        <h2 id="recipe-ingredients-title">Ingredients</h2>
      </div>
      <div class="recipe-serving-picker">
        <label for="recipe-servings">Servings</label>
        <div class="servings-control" data-stepper data-recipe-servings data-base-servings="<?= $baseServings ?>">
          <button type="button" class="btn btn-outline btn-sm" data-step="-1" aria-label="Decrease servings">&minus;</button>
          <input id="recipe-servings" type="number" value="<?= $baseServings ?>" min="1" aria-label="Serving count">
          <button type="button" class="btn btn-outline btn-sm" data-step="1" aria-label="Increase servings">+</button>
        </div>
      </div>
    </div>

    <?php if (!$ingredients): ?>
      <p class="text-muted">No ingredients have been added yet.</p>
    <?php else: ?>
      <ul class="recipe-ingredient-list">
        <?php foreach ($ingredients as $ingredient): ?>
          <?php $baseQuantity = (float) ($ingredient['quantity'] ?? 0); ?>
          <li>
            <span class="recipe-ingredient-name"><?= htmlspecialchars($ingredient['ingredient_name'] ?? 'Ingredient') ?></span>
            <span class="recipe-ingredient-amount">
              <strong data-ingredient-quantity data-base-quantity="<?= htmlspecialchars((string) $baseQuantity) ?>"><?= rtrim(rtrim(number_format($baseQuantity, 2, '.', ''), '0'), '.') ?></strong>
              <?= htmlspecialchars($ingredient['unit'] ?? '') ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section class="card recipe-instructions-card" aria-labelledby="recipe-instructions-title">
    <p class="hero-eyebrow">How to make it</p>
    <h2 id="recipe-instructions-title">Instructions</h2>
    <?php if (!$instructionSteps): ?>
      <p class="text-muted">No cooking instructions have been added yet.</p>
    <?php else: ?>
      <ol class="recipe-instruction-list">
        <?php foreach ($instructionSteps as $step): ?>
          <li><span><?= htmlspecialchars($step) ?></span></li>
        <?php endforeach; ?>
      </ol>
    <?php endif; ?>
  </section>
</div>

<section class="recipe-reviews-section">
  <h2>Reviews</h2>
  <?php if (!$reviews): ?>
    <div class="card text-muted">No reviews yet.</div>
  <?php else: ?>
    <div class="recipe-review-list">
      <?php foreach ($reviews as $review): ?>
        <article class="card recipe-review-card">
          <div class="flex-between">
            <strong><?= htmlspecialchars($review['username'] ?? 'Customer') ?></strong>
            <span class="badge badge-success"><?= (int) ($review['rating'] ?? 0) ?> &#9733;</span>
          </div>
          <?php if (trim((string) ($review['comment'] ?? '')) !== ''): ?>
            <p><?= nl2br(htmlspecialchars((string) $review['comment'])) ?></p>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php if (AuthHelper::check()): ?>
  <section class="recipe-feedback-grid">
    <?php $currentUserReview = $currentUserReview ?? null; ?>
    <form method="post" action="<?= BASE_URL ?>/recipes/<?= (int) ($recipe['recipe_id'] ?? 0) ?>/reviews" class="card">
      <?= Csrf::field() ?>
      <h3><?= $currentUserReview ? 'Edit your review' : 'Write a review' ?></h3>
      <label for="recipe-review-rating">Rating</label>
      <select id="recipe-review-rating" name="rating">
        <?php foreach ([5, 4, 3, 2, 1] as $rating): ?>
          <option value="<?= $rating ?>" <?= (int) ($currentUserReview['rating'] ?? 5) === $rating ? 'selected' : '' ?>><?= $rating ?> &#9733;</option>
        <?php endforeach; ?>
      </select>
      <label for="recipe-review-comment">Comment</label>
      <textarea id="recipe-review-comment" name="comment"><?= htmlspecialchars($currentUserReview['comment'] ?? '') ?></textarea>
      <button class="btn btn-primary mt-1"><?= $currentUserReview ? 'Update review' : 'Submit review' ?></button>
    </form>

    <form method="post" action="<?= BASE_URL ?>/reports" class="card">
      <?= Csrf::field() ?>
      <h3>Report recipe</h3>
      <input type="hidden" name="target_type" value="recipe">
      <input type="hidden" name="target_id" value="<?= (int) ($recipe['recipe_id'] ?? 0) ?>">
      <label for="recipe-report-reason">Reason</label>
      <textarea id="recipe-report-reason" name="reason" required></textarea>
      <button class="btn btn-outline mt-1">Submit report</button>
    </form>
  </section>
<?php endif; ?>
