<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class SavedRecipe extends Model
{
    protected string $table = 'saved_recipes';
    protected string $primaryKey = 'saved_id';

    public function isSaved(int $userId, int $recipeId): bool
    {
        $rows = $this->query(
            'SELECT saved_id FROM saved_recipes WHERE user_id = :u AND recipe_id = :r LIMIT 1',
            [':u' => $userId, ':r' => $recipeId]
        );

        return !empty($rows);
    }

    public function toggle(int $userId, int $recipeId): string
    {
        $stmt = $this->db()->prepare('SELECT saved_id FROM saved_recipes WHERE user_id=:u AND recipe_id=:r');
        $stmt->execute([':u' => $userId, ':r' => $recipeId]);
        if ($row = $stmt->fetch()) {
            $this->delete((int) $row['saved_id']);
            return 'removed';
        }
        $this->insert(['user_id' => $userId, 'recipe_id' => $recipeId]);
        return 'saved';
    }

    public function forUser(int $userId): array
    {
        return $this->query(
            "SELECT sr.*, r.* FROM saved_recipes sr
             JOIN recipes r ON r.recipe_id = sr.recipe_id
             WHERE sr.user_id = :u AND r.status = 'active'
             ORDER BY sr.saved_at DESC",
            [':u' => $userId]
        );
    }
}
