<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class CartItem extends Model
{
    protected string $table = 'cart_items';
    protected string $primaryKey = 'cart_item_id';

    public function countForUser(int $userId): int
    {
        $rows = $this->query(
            "SELECT COUNT(ci.cart_item_id) AS item_count
             FROM carts c
             LEFT JOIN cart_items ci ON ci.cart_id = c.cart_id
             WHERE c.user_id = :user_id",
            [':user_id' => $userId]
        );

        return empty($rows) ? 0 : (int) ($rows[0]['item_count'] ?? 0);
    }

    /** All items in the cart joined with product and store info. */
    public function detailed(int $cartId): array
    {
        return $this->query(
            "SELECT ci.*, p.product_name, p.image, p.stock_quantity, p.package_quantity, p.package_unit,
                    p.store_id, s.store_name
             FROM cart_items ci
             JOIN products p ON p.product_id = ci.product_id
             JOIN stores s   ON s.store_id   = p.store_id
             WHERE ci.cart_id = :c
             ORDER BY s.store_name, ci.cart_item_id",
            [':c' => $cartId]
        );
    }

    public function addOrIncrement(int $cartId, int $productId, int $qty, float $unitPrice, string $method = 'manual', ?int $recipeId = null, ?int $riId = null): int
    {
        // Manual adds dedupe by product. Recipe adds dedupe by (product, recipe, recipe_ingredient).
        $sql = "SELECT cart_item_id, quantity FROM cart_items
                WHERE cart_id = :c AND product_id = :p AND added_method = :m
                  AND COALESCE(recipe_id,0) = :ri AND COALESCE(recipe_ingredient_id,0) = :rii
                LIMIT 1";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':c'   => $cartId,
            ':p'   => $productId,
            ':m'   => $method,
            ':ri'  => $recipeId ?? 0,
            ':rii' => $riId ?? 0,
        ]);
        $row = $stmt->fetch();
        if ($row) {
            $newQty = (int) $row['quantity'] + $qty;
            $this->update((int) $row['cart_item_id'], ['quantity' => $newQty]);
            return (int) $row['cart_item_id'];
        }
        return $this->insert([
            'cart_id'              => $cartId,
            'product_id'           => $productId,
            'recipe_id'            => $recipeId,
            'recipe_ingredient_id' => $riId,
            'quantity'             => $qty,
            'unit_price'           => $unitPrice,
            'added_method'         => $method,
        ]);
    }
}
