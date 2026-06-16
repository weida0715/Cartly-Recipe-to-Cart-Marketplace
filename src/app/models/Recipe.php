<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Recipe extends Model
{
    protected string $table = 'recipes';
    protected string $primaryKey = 'recipe_id';

    public function paginateActive(string $search = '', string $cuisine = '', string $difficulty = '', string $sort = 'newest', int $page = 1, int $perPage = 12): array
    {
        $baseSql = "FROM recipes r
                JOIN users u ON u.user_id = r.user_id
                LEFT JOIN (
                    SELECT recipe_id, AVG(rating) AS review_rating, COUNT(*) AS review_count
                    FROM reviews
                    WHERE status='visible' AND recipe_id IS NOT NULL
                    GROUP BY recipe_id
                ) rv ON rv.recipe_id = r.recipe_id
                WHERE r.status='active'";
        $params = [];
        if ($search !== '') {
            $baseSql .= " AND (r.recipe_title LIKE :q_title ESCAPE '\\\\' OR r.description LIKE :q_description ESCAPE '\\\\')";
            $like = $this->containsLikePattern($search);
            $params[':q_title'] = $like;
            $params[':q_description'] = $like;
        }
        if ($cuisine !== '') {
            $baseSql .= " AND r.cuisine_type LIKE :cuisine ESCAPE '\\\\'";
            $params[':cuisine'] = $this->containsLikePattern($cuisine);
        }
        if ($difficulty !== '') {
            $baseSql .= " AND r.difficulty = :difficulty";
            $params[':difficulty'] = $difficulty;
        }

        $orderBy = match ($sort) {
            'oldest' => 'r.created_at ASC, r.recipe_id ASC',
            'title_asc' => 'r.recipe_title ASC, r.recipe_id ASC',
            'title_desc' => 'r.recipe_title DESC, r.recipe_id DESC',
            'prep_asc' => 'r.prep_time ASC, r.recipe_id ASC',
            'prep_desc' => 'r.prep_time DESC, r.recipe_id DESC',
            'review_desc' => 'COALESCE(rv.review_rating, 0) DESC, COALESCE(rv.review_count, 0) DESC, r.recipe_id DESC',
            'review_count_desc' => 'COALESCE(rv.review_count, 0) DESC, COALESCE(rv.review_rating, 0) DESC, r.recipe_id DESC',
            default => 'r.created_at DESC, r.recipe_id DESC',
        };

        $countRows = $this->query("SELECT COUNT(*) AS cnt {$baseSql}", $params);
        $total = (int) ($countRows[0]['cnt'] ?? 0);
        $offset = max(0, ($page - 1) * $perPage);

        $rows = $this->query(
            "SELECT r.*, u.username,
                    COALESCE(rv.review_rating, 0) AS review_rating,
                    COALESCE(rv.review_count, 0) AS review_count
             {$baseSql} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function active(string $search = ''): array
    {
        $sql = "SELECT r.*, u.username FROM recipes r
                JOIN users u ON u.user_id = r.user_id
                WHERE r.status='active'";
        $p = [];
        if ($search !== '') {
            $sql .= " AND (r.recipe_title LIKE :q_title ESCAPE '\\\\' OR r.description LIKE :q_description ESCAPE '\\\\')";
            $like = $this->containsLikePattern($search);
            $p[':q_title'] = $like;
            $p[':q_description'] = $like;
        }
        $sql .= " ORDER BY r.created_at DESC";
        return $this->query($sql, $p);
    }

    public function byUser(int $userId): array
    {
        return $this->where('user_id', $userId, 'created_at DESC');
    }

    private function containsLikePattern(string $value): string
    {
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
        return '%' . $escaped . '%';
    }
}
