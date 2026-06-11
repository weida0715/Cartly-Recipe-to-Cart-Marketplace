<?php use App\Helpers\Csrf; ?>
<h2>Edit recipe</h2>
<form method="post" action="<?= BASE_URL ?>/recipes/<?= (int) $recipe['recipe_id'] ?>/update" class="card"
  enctype="multipart/form-data">
  <?= Csrf::field() ?>
  <div class="form-row"><label>Title</label><input name="recipe_title"
      value="<?= htmlspecialchars($recipe['recipe_title']) ?>" required></div>
  <div class="form-grid">
    <div class="form-row"><label>Cuisine</label><input name="cuisine_type"
        value="<?= htmlspecialchars($recipe['cuisine_type']) ?>"></div>
    <div class="form-row">
      <label>Difficulty</label>
      <select name="difficulty">
        <?php foreach (['easy', 'medium', 'hard'] as $d): ?>
          <option <?= $recipe['difficulty'] === $d ? 'selected' : '' ?>><?= $d ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="form-grid">
    <div class="form-row"><label>Base servings</label><input type="number" name="base_servings"
        value="<?= (int) $recipe['base_servings'] ?>"></div>
    <div class="form-row"><label>Prep time</label><input type="number" name="prep_time"
        value="<?= (int) $recipe['prep_time'] ?>"></div>
    <div class="form-row"><label>Cook time</label><input type="number" name="cook_time"
        value="<?= (int) $recipe['cook_time'] ?>"></div>
  </div>
  <div class="form-row"><label>Description</label><textarea
      name="description"><?= htmlspecialchars($recipe['description']) ?></textarea></div>
  <div class="form-row">
    <label>Recipe image</label>
    <?php if (!empty($recipe['image'])): ?>
      <p class="text-muted">Current: <?= htmlspecialchars($recipe['image']) ?></p><?php endif; ?>
    <input type="file" name="image" accept="image/*">
  </div>
  <div class="form-row"><label>Instructions</label><textarea
      name="instructions"><?= htmlspecialchars($recipe['instructions']) ?></textarea></div>
  <div class="form-row">
    <label>Status</label>
    <select name="status">
      <?php foreach (['active', 'hidden', 'removed'] as $s): ?>
        <option <?= $recipe['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <h3>Ingredients</h3>
  <table class="table">
    <thead>
      <tr>
        <th>Ingredient</th>
        <th>Quantity</th>
        <th>Unit</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $rows2 = $rows;
      while (count($rows2) < 5)
        $rows2[] = ['ingredient_id' => '', 'quantity' => '', 'unit' => ''];
      foreach ($rows2 as $i => $row): ?>
        <tr>
          <td>
            <select name="ingredients[<?= $i ?>][ingredient_id]">
              <option value="">— none —</option>
              <?php foreach ($ingredients as $ing): ?>
                <option value="<?= (int) $ing['ingredient_id'] ?>"
                  <?= (int) $row['ingredient_id'] === (int) $ing['ingredient_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($ing['ingredient_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>
          <td><input type="number" step="0.01" name="ingredients[<?= $i ?>][quantity]"
              value="<?= htmlspecialchars((string) $row['quantity']) ?>"></td>
          <td><input name="ingredients[<?= $i ?>][unit]" value="<?= htmlspecialchars((string) $row['unit']) ?>"></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <button class="btn btn-primary mt-2">Save changes</button>
</form>