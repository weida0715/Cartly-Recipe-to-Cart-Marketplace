<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Review extends Model
{
    protected string $table = 'reviews';
    protected string $primaryKey = 'review_id';

    public function forProduct(int $productId): array
    {
        return $this->query(
            "SELECT r.*, u.username FROM reviews r
             JOIN users u ON u.user_id = r.user_id
             WHERE r.product_id = :p AND r.status='visible'
             ORDER BY r.created_at DESC",
            [':p' => $productId]
        );
    }

    public function forRecipe(int $recipeId): array
    {
        return $this->query(
            "SELECT r.*, u.username FROM reviews r
             JOIN users u ON u.user_id = r.user_id
             WHERE r.recipe_id = :r AND r.status='visible'
             ORDER BY r.created_at DESC",
            [':r' => $recipeId]
        );
    }

    public function addProductReview(int $userId, int $productId, int $rating, string $comment): int
    {
        return $this->insert([
            'user_id' => $userId,
            'product_id' => $productId,
            'recipe_id' => null,
            'rating' => max(1, min(5, $rating)),
            'comment' => $comment,
            'status' => 'visible',
        ]);
    }

    public function addRecipeReview(int $userId, int $recipeId, int $rating, string $comment): int
    {
        return $this->insert([
            'user_id' => $userId,
            'product_id' => null,
            'recipe_id' => $recipeId,
            'rating' => max(1, min(5, $rating)),
            'comment' => $comment,
            'status' => 'visible',
        ]);
    }
}
