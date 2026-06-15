<?php
declare(strict_types=1);
namespace App\Controllers\Recipe;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Helpers\FileUploadHelper;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Ingredient;
use App\Models\SavedRecipe;
use App\Models\Review;

class RecipeController extends Controller
{
    public function index(): void
    {
        $q = trim((string) $this->input('q', ''));
        $cuisine = trim((string) $this->input('cuisine', ''));
        $difficulty = trim((string) $this->input('difficulty', ''));
        $sort = trim((string) $this->input('sort', 'newest'));
        $page = max(1, (int) $this->input('page', 1));
        $result = (new Recipe())->paginateActive($q, $cuisine, $difficulty, $sort, $page, 12);
        $this->view('recipe/index', [
            'title' => 'Recipes · Cartly',
            'recipes' => $result['rows'],
            'q' => $q,
            'cuisine' => $cuisine,
            'difficulty' => $difficulty,
            'sort' => $sort,
            'page' => $result['page'],
            'pages' => $result['pages'],
            'total' => $result['total'],
        ]);
    }

    public function show(string $id): void
    {
        $recipe = (new Recipe())->find((int) $id);
        if (!$recipe) {
            http_response_code(404);
            echo 'Recipe not found';
            return;
        }
        $this->view('recipe/show', [
            'title' => $recipe['recipe_title'] . ' · Cartly',
            'recipe' => $recipe,
            'ingredients' => (new RecipeIngredient())->detailed((int) $id),
            'reviews' => (new Review())->forRecipe((int) $id),
            'isSaved' => AuthHelper::check()
                ? (new SavedRecipe())->isSaved((int) AuthHelper::id(), (int) $id)
                : false,
        ]);
    }

    public function create(): void
    {
        AuthHelper::requireLogin();
        $this->view('recipe/create', [
            'title' => 'New Recipe',
            'ingredients' => (new Ingredient())->all('ingredient_name'),
        ]);
    }

    public function store(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $title = trim((string) $this->input('recipe_title', ''));
        if ($title === '') {
            Flash::set('error', 'Recipe title is required.');
            $this->redirect('/recipes/create');
        }

        $r = new Recipe();
        try {
            $image = FileUploadHelper::image('image', 'recipes');
        } catch (\RuntimeException $e) {
            Flash::set('error', $e->getMessage());
            $this->redirect('/recipes/create');
        }

        $id = $r->insert([
            'user_id' => (int) AuthHelper::id(),
            'recipe_title' => $title,
            'description' => (string) $this->input('description', ''),
            'instructions' => (string) $this->input('instructions', ''),
            'base_servings' => max(1, (int) $this->input('base_servings', 1)),
            'cuisine_type' => (string) $this->input('cuisine_type', ''),
            'difficulty' => (string) $this->input('difficulty', 'easy'),
            'prep_time' => (int) $this->input('prep_time', 0),
            'cook_time' => (int) $this->input('cook_time', 0),
            'image' => $image,
            'status' => 'active',
        ]);

        $ing = $_POST['ingredients'] ?? [];
        $ri = new RecipeIngredient();
        foreach ($ing as $row) {
            if (empty($row['ingredient_id']))
                continue;
            $ri->insert([
                'recipe_id' => $id,
                'ingredient_id' => (int) $row['ingredient_id'],
                'quantity' => (float) ($row['quantity'] ?? 0),
                'unit' => (string) ($row['unit'] ?? ''),
            ]);
        }
        Flash::set('success', 'Recipe created.');
        $this->redirect('/recipes/' . $id);
    }

    public function edit(string $id): void
    {
        AuthHelper::requireLogin();
        $recipe = (new Recipe())->find((int) $id);
        if (!$recipe || (int) $recipe['user_id'] !== (int) AuthHelper::id()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }
        $this->view('recipe/edit', [
            'title' => 'Edit Recipe',
            'recipe' => $recipe,
            'ingredients' => (new Ingredient())->all('ingredient_name'),
            'rows' => (new RecipeIngredient())->detailed((int) $id),
        ]);
    }

    public function update(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $r = new Recipe();
        $recipe = $r->find((int) $id);
        if (!$recipe || (int) $recipe['user_id'] !== (int) AuthHelper::id()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }
        $title = trim((string) $this->input('recipe_title', ''));
        if ($title === '') {
            Flash::set('error', 'Recipe title is required.');
            $this->redirect('/recipes/' . $id . '/edit');
        }

        $data = [
            'recipe_title' => $title,
            'description' => (string) $this->input('description', ''),
            'instructions' => (string) $this->input('instructions', ''),
            'base_servings' => max(1, (int) $this->input('base_servings', 1)),
            'cuisine_type' => (string) $this->input('cuisine_type', ''),
            'difficulty' => (string) $this->input('difficulty', 'easy'),
            'prep_time' => (int) $this->input('prep_time', 0),
            'cook_time' => (int) $this->input('cook_time', 0),
            'status' => (string) $this->input('status', 'active'),
        ];
        try {
            $image = FileUploadHelper::image('image', 'recipes');
            if ($image !== null) {
                $data['image'] = $image;
            }
        } catch (\RuntimeException $e) {
            Flash::set('error', $e->getMessage());
            $this->redirect('/recipes/' . $id . '/edit');
        }
        $r->update((int) $id, $data);
        $ri = new RecipeIngredient();
        $ri->deleteByRecipe((int) $id);
        foreach (($_POST['ingredients'] ?? []) as $row) {
            if (empty($row['ingredient_id']))
                continue;
            $ri->insert([
                'recipe_id' => (int) $id,
                'ingredient_id' => (int) $row['ingredient_id'],
                'quantity' => (float) ($row['quantity'] ?? 0),
                'unit' => (string) ($row['unit'] ?? ''),
            ]);
        }
        Flash::set('success', 'Recipe updated.');
        $this->redirect('/recipes/' . $id);
    }

    public function save(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $state = (new SavedRecipe())->toggle((int) AuthHelper::id(), (int) $id);
        Flash::set('info', $state === 'saved' ? 'Saved to your recipes.' : 'Removed from saved.');
        $this->redirect('/recipes/' . $id);
    }

    public function hide(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $recipe = (new Recipe())->find((int) $id);
        if (!$recipe || (int) $recipe['user_id'] !== (int) AuthHelper::id()) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }
        (new Recipe())->update((int) $id, ['status' => 'hidden']);
        Flash::set('info', 'Recipe hidden.');
        $this->redirect('/dashboard');
    }
}
