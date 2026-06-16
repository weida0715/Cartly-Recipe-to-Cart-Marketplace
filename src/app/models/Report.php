<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Report extends Model
{
    protected string $table = 'reports';
    protected string $primaryKey = 'report_id';

    public function moderationList(): array
    {
        return $this->query(
            "SELECT r.*, u.username AS reporter,
                    p.product_name, p.description AS product_description, p.price AS product_price,
                    p.stock_quantity AS product_stock_quantity, p.package_quantity AS product_package_quantity,
                    p.package_unit AS product_package_unit, p.status AS product_status,
                    ps.store_name AS product_store_name, pc.category_name AS product_category_name,
                    pi.ingredient_name AS product_ingredient_name,
                    rec.recipe_title, rec.status AS recipe_status, rec.cuisine_type AS recipe_cuisine_type,
                    rec.difficulty AS recipe_difficulty, rec.prep_time AS recipe_prep_time,
                    rec.cook_time AS recipe_cook_time, ru.username AS recipe_author,
                    rv.rating AS review_rating, rv.comment AS review_comment, rv.status AS review_status,
                    rvu.username AS review_author, rvp.product_name AS review_product_name,
                    rvr.recipe_title AS review_recipe_title
             FROM reports r
             JOIN users u ON u.user_id = r.user_id
             LEFT JOIN products p ON r.target_type = 'product' AND p.product_id = r.target_id
             LEFT JOIN stores ps ON ps.store_id = p.store_id
             LEFT JOIN categories pc ON pc.category_id = p.category_id
             LEFT JOIN ingredients pi ON pi.ingredient_id = p.ingredient_id
             LEFT JOIN recipes rec ON r.target_type = 'recipe' AND rec.recipe_id = r.target_id
             LEFT JOIN users ru ON ru.user_id = rec.user_id
             LEFT JOIN reviews rv ON r.target_type = 'review' AND rv.review_id = r.target_id
             LEFT JOIN users rvu ON rvu.user_id = rv.user_id
             LEFT JOIN products rvp ON rvp.product_id = rv.product_id
             LEFT JOIN recipes rvr ON rvr.recipe_id = rv.recipe_id
             ORDER BY FIELD(r.status, 'pending', 'reviewed', 'resolved'), r.created_at DESC"
        );
    }

    public function statusCounts(): array
    {
        $counts = [
            'pending' => 0,
            'reviewed' => 0,
            'resolved' => 0,
            'total' => 0,
        ];

        $rows = $this->query('SELECT status, COUNT(*) AS total FROM reports GROUP BY status');
        foreach ($rows as $row) {
            $status = (string) $row['status'];
            $total = (int) $row['total'];
            if (array_key_exists($status, $counts)) {
                $counts[$status] = $total;
            }
            $counts['total'] += $total;
        }

        return $counts;
    }

    public function createForUser(int $userId, string $targetType, int $targetId, string $reason): int
    {
        return $this->insert([
            'user_id' => $userId,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'reason' => $reason,
            'status' => 'pending',
        ]);
    }
}
