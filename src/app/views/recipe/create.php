<?php use App\Helpers\Csrf; ?>
<h2>New recipe</h2>
<form method="post" action="<?= BASE_URL ?>/recipes" class="card" enctype="multipart/form-data">
  <?= Csrf::field() ?>
  <div class="form-row"><label>Title</label><input name="recipe_title" required></div>
  <div class="form-grid">
    <div class="form-row"><label>Cuisine</label><input name="cuisine_type"></div>
    <div class="form-row">
      <label>Difficulty</label>
      <select name="difficulty">
        <option>easy</option>
        <option>medium</option>
        <option>hard</option>
      </select>
    </div>
  </div>
  <div class="form-grid">
    <div class="form-row"><label>Base servings</label><input type="number" name="base_servings" value="2" min="1"></div>
    <div class="form-row"><label>Prep time (min)</label><input type="number" name="prep_time" value="10"></div>
    <div class="form-row"><label>Cook time (min)</label><input type="number" name="cook_time" value="20"></div>
  </div>
  <div class="form-row"><label>Description</label><textarea name="description"></textarea></div>
  <div class="form-row"><label>Recipe image</label><input type="file" name="image" accept="image/*"></div>
  <div class="form-row"><label>Instructions</label><textarea name="instructions"></textarea></div>

  <h3>Ingredients</h3>
  <table class="table" id="ing-table">
    <thead>
      <tr>
        <th>Ingredient</th>
        <th>Quantity</th>
        <th>Unit</th>
      </tr>
    </thead>
    <tbody>
      <?php for ($i = 0; $i < 5; $i++): ?>
        <tr>
          <td>
            <select name="ingredients[<?= $i ?>][ingredient_id]">
              <option value="">— none —</option>
              <?php foreach ($ingredients as $ing): ?>
                <option value="<?= (int) $ing['ingredient_id'] ?>"><?= htmlspecialchars($ing['ingredient_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </td>
          <td><input type="number" step="0.01" name="ingredients[<?= $i ?>][quantity]" value="0"></td>
          <td><input name="ingredients[<?= $i ?>][unit]" placeholder="g, ml, pcs"></td>
        </tr>
      <?php endfor; ?>
    </tbody>
  </table>

  <button class="btn btn-primary mt-2">Create recipe</button>
</form>