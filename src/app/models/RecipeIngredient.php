<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class RecipeIngredient extends Model
{
    protected string $table = 'recipe_ingredients';
    protected string $primaryKey = 'recipe_ingredient_id';

    public function detailed(int $recipeId): array
    {
        return $this->query(
            "SELECT ri.*, i.ingredient_name, i.base_unit
             FROM recipe_ingredients ri
             JOIN ingredients i ON i.ingredient_id = ri.ingredient_id
             WHERE ri.recipe_id = :r
             ORDER BY ri.recipe_ingredient_id",
            [':r' => $recipeId]
        );
    }

    public function deleteByRecipe(int $recipeId): void
    {
        $this->db()->prepare('DELETE FROM recipe_ingredients WHERE recipe_id = :r')->execute([':r' => $recipeId]);
    }
}
