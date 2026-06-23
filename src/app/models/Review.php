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

    public function findForProductByUser(int $userId, int $productId): ?array
    {
        $rows = $this->query(
            'SELECT * FROM reviews WHERE user_id = :u AND product_id = :p LIMIT 1',
            [':u' => $userId, ':p' => $productId]
        );
        return $rows[0] ?? null;
    }

    public function findForRecipeByUser(int $userId, int $recipeId): ?array
    {
        $rows = $this->query(
            'SELECT * FROM reviews WHERE user_id = :u AND recipe_id = :r LIMIT 1',
            [':u' => $userId, ':r' => $recipeId]
        );
        return $rows[0] ?? null;
    }

    public function countForUser(int $userId): int
    {
        $rows = $this->query(
            'SELECT COUNT(*) AS total FROM reviews WHERE user_id = :u',
            [':u' => $userId]
        );
        return (int) ($rows[0]['total'] ?? 0);
    }

    public function saveProductReview(int $userId, int $productId, int $rating, string $comment): int
    {
        $existing = $this->findForProductByUser($userId, $productId);
        if ($existing) {
            $this->update((int) $existing['review_id'], [
                'rating' => max(1, min(5, $rating)),
                'comment' => $comment,
                'status' => 'visible',
            ]);
            return (int) $existing['review_id'];
        }

        return $this->addProductReview($userId, $productId, $rating, $comment);
    }

    public function saveRecipeReview(int $userId, int $recipeId, int $rating, string $comment): int
    {
        $existing = $this->findForRecipeByUser($userId, $recipeId);
        if ($existing) {
            $this->update((int) $existing['review_id'], [
                'rating' => max(1, min(5, $rating)),
                'comment' => $comment,
                'status' => 'visible',
            ]);
            return (int) $existing['review_id'];
        }

        return $this->addRecipeReview($userId, $recipeId, $rating, $comment);
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
