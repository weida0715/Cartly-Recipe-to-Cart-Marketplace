<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Product extends Model
{
    protected string $table = 'products';
    protected string $primaryKey = 'product_id';

    public function paginateActive(string $search = '', ?int $categoryId = null, string $sort = 'newest', int $page = 1, int $perPage = 16): array
    {
        $baseSql = "FROM products p
                JOIN stores s    ON s.store_id = p.store_id
                LEFT JOIN categories c ON c.category_id = p.category_id
                LEFT JOIN ingredients i ON i.ingredient_id = p.ingredient_id
                LEFT JOIN (
                    SELECT product_id, AVG(rating) AS review_rating, COUNT(*) AS review_count
                    FROM reviews
                    WHERE status='visible' AND product_id IS NOT NULL
                    GROUP BY product_id
                ) rv ON rv.product_id = p.product_id
                WHERE p.status='active' AND s.store_status='approved'";
        $params = [];
        if ($search !== '') {
            $baseSql .= " AND (p.product_name LIKE :q OR p.description LIKE :q OR i.ingredient_name LIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }
        if ($categoryId) {
            $baseSql .= " AND p.category_id = :cid";
            $params[':cid'] = $categoryId;
        }

        $orderBy = match ($sort) {
            'price_asc' => 'p.price ASC, p.product_id ASC',
            'price_desc' => 'p.price DESC, p.product_id DESC',
            'rating_desc' => 'p.rating DESC, p.product_id DESC',
            'rating_asc' => 'p.rating ASC, p.product_id ASC',
            'review_desc' => 'COALESCE(rv.review_rating, 0) DESC, COALESCE(rv.review_count, 0) DESC, p.product_id DESC',
            'review_count_desc' => 'COALESCE(rv.review_count, 0) DESC, COALESCE(rv.review_rating, 0) DESC, p.product_id DESC',
            'name_asc' => 'p.product_name ASC, p.product_id ASC',
            'name_desc' => 'p.product_name DESC, p.product_id DESC',
            default => 'p.created_at DESC, p.product_id DESC',
        };

        $countRows = $this->query("SELECT COUNT(*) AS cnt {$baseSql}", $params);
        $total = (int) ($countRows[0]['cnt'] ?? 0);
        $offset = max(0, ($page - 1) * $perPage);

        $rows = $this->query(
            "SELECT p.*, s.store_name, s.rating AS store_rating, c.category_name, i.ingredient_name,
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

    public function active(string $search = '', ?int $categoryId = null): array
    {
        $sql = "SELECT p.*, s.store_name, s.rating AS store_rating, c.category_name, i.ingredient_name
                FROM products p
                JOIN stores s    ON s.store_id = p.store_id
                LEFT JOIN categories c ON c.category_id = p.category_id
                LEFT JOIN ingredients i ON i.ingredient_id = p.ingredient_id
                WHERE p.status='active' AND s.store_status='approved'";
        $params = [];
        if ($search !== '') {
            $sql .= " AND (p.product_name LIKE :q OR p.description LIKE :q OR i.ingredient_name LIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }
        if ($categoryId) {
            $sql .= " AND p.category_id = :cid";
            $params[':cid'] = $categoryId;
        }
        $sql .= " ORDER BY p.created_at DESC";
        return $this->query($sql, $params);
    }

    public function findWithStore(int $id): ?array
    {
        $rows = $this->query(
            "SELECT p.*, s.store_name, s.rating AS store_rating, c.category_name
             FROM products p
             JOIN stores s ON s.store_id = p.store_id
             LEFT JOIN categories c ON c.category_id = p.category_id
             WHERE p.product_id = :id LIMIT 1",
            [':id' => $id]
        );
        return $rows[0] ?? null;
    }

    public function byStore(int $storeId): array
    {
        return $this->where('store_id', $storeId, 'created_at DESC');
    }

    /** Products that match a standard ingredient and are purchasable. */
    public function activeByIngredient(int $ingredientId): array
    {
        return $this->query(
            "SELECT p.*, s.store_name, s.rating AS store_rating
             FROM products p
             JOIN stores s ON s.store_id = p.store_id
             WHERE p.ingredient_id = :iid
               AND p.status='active'
               AND s.store_status='approved'
               AND p.stock_quantity > 0",
            [':iid' => $ingredientId]
        );
    }

    public function decrementStock(int $productId, int $qty): void
    {
        $stmt = $this->db()->prepare(
            'UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - :q) WHERE product_id = :id'
        );
        $stmt->execute([':q' => $qty, ':id' => $productId]);
    }
}
