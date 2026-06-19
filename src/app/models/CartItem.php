<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class CartItem extends Model
{
    protected string $table = 'cart_items';
    protected string $primaryKey = 'cart_item_id';

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

    public function findForUser(int $cartItemId, int $userId): ?array
    {
        $rows = $this->query(
            "SELECT ci.*, p.product_name, p.stock_quantity
             FROM cart_items ci
             JOIN carts c ON c.cart_id = ci.cart_id
             JOIN products p ON p.product_id = ci.product_id
             WHERE ci.cart_item_id = :ci AND c.user_id = :u
             LIMIT 1",
            [':ci' => $cartItemId, ':u' => $userId]
        );
        return $rows[0] ?? null;
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
