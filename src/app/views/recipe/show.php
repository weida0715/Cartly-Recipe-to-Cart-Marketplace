<?php use App\Helpers\Csrf;
use App\Helpers\AuthHelper; ?>
<div class="grid grid-2">
  <div>
    <h2><?= htmlspecialchars($recipe['recipe_title']) ?></h2>
    <?php if (!empty($recipe['image'])): ?>
      <img class="recipe-image" src="<?= UPLOAD_URL ?>/<?= htmlspecialchars($recipe['image']) ?>"
        alt="<?= htmlspecialchars($recipe['recipe_title']) ?>">
    <?php endif; ?>
    <p class="text-muted"><?= htmlspecialchars($recipe['cuisine_type']) ?> ·
      <?= htmlspecialchars($recipe['difficulty']) ?> · serves <?= (int) $recipe['base_servings'] ?>
    </p>
    <p><?= nl2br(htmlspecialchars($recipe['description'] ?? '')) ?></p>

    <h3>Ingredients</h3>
    <ul>
      <?php foreach ($ingredients as $i): ?>
        <li><?= number_format((float) $i['quantity'], 2) ?>   <?= htmlspecialchars($i['unit']) ?> &mdash;
          <?= htmlspecialchars($i['ingredient_name']) ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <h3>Instructions</h3>
    <p><?= nl2br(htmlspecialchars($recipe['instructions'] ?? '')) ?></p>

    <section class="mt-3">
      <h3>Reviews</h3>
      <?php if (empty($reviews)): ?>
        <p class="text-muted">No reviews yet.</p>
      <?php else:
        foreach ($reviews as $r): ?>
          <div class="card">
            <strong><?= htmlspecialchars($r['username']) ?></strong>
            <span class="badge badge-success"><?= (int) $r['rating'] ?> ★</span>
            <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
          </div>
        <?php endforeach; endif; ?>
    </section>

    <?php if (AuthHelper::check()): ?>
      <section class="mt-3 grid grid-2">
        <form method="post" action="<?= BASE_URL ?>/recipes/<?= (int) $recipe['recipe_id'] ?>/reviews" class="card">
          <?= Csrf::field() ?>
          <h3>Write a review</h3>
          <select name="rating">
            <?php foreach ([5, 4, 3, 2, 1] as $rating): ?>
              <option value="<?= $rating ?>"><?= $rating ?> ★</option>
            <?php endforeach; ?>
          </select>
          <textarea name="comment" required></textarea>
          <button class="btn btn-primary mt-1">Submit review</button>
        </form>
        <form method="post" action="<?= BASE_URL ?>/reports" class="card">
          <?= Csrf::field() ?>
          <h3>Report recipe</h3>
          <input type="hidden" name="target_type" value="recipe">
          <input type="hidden" name="target_id" value="<?= (int) $recipe['recipe_id'] ?>">
          <textarea name="reason" required></textarea>
          <button class="btn btn-outline mt-1">Submit report</button>
        </form>
      </section>
    <?php endif; ?>
  </div>

  <div>
    <div class="card">
      <h3>Recipe → Cart</h3>
      <p class="text-muted">Generate cart items from this recipe.</p>
      <?php if (AuthHelper::check()): ?>
        <form method="post" action="<?= BASE_URL ?>/recipes/<?= (int) $recipe['recipe_id'] ?>/preview-cart">
          <?= Csrf::field() ?>
          <label>Servings</label>
          <div class="servings-control" data-stepper>
            <button type="button" class="btn btn-outline btn-sm" data-step="-1">−</button>
            <input type="number" name="servings" value="<?= (int) $recipe['base_servings'] ?>" min="1">
            <button type="button" class="btn btn-outline btn-sm" data-step="1">+</button>
          </div>
          <button class="btn btn-primary btn-block mt-2">Preview cart</button>
        </form>
        <form method="post" action="<?= BASE_URL ?>/recipes/<?= (int) $recipe['recipe_id'] ?>/save" class="mt-2">
          <?= Csrf::field() ?>
          <button class="btn btn-outline btn-block" type="submit" aria-pressed="<?= !empty($isSaved) ? 'true' : 'false' ?>">
            <?= !empty($isSaved) ? 'Unsave recipe' : 'Save recipe' ?>
          </button>
        </form>
        <?php if ((int) $recipe['user_id'] === (int) AuthHelper::id()): ?>
          <form method="post" action="<?= BASE_URL ?>/recipes/<?= (int) $recipe['recipe_id'] ?>/hide" class="mt-2">
            <?= Csrf::field() ?>
            <button class="btn btn-outline btn-block">Hide recipe</button>
          </form>
        <?php endif; ?>
      <?php else: ?>
        <a class="btn btn-primary btn-block" href="<?= BASE_URL ?>/auth/login">Login to generate cart</a>
      <?php endif; ?>
    </div>
  </div>
</div>
