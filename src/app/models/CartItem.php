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
        return $rows ? $rows[0] : null;
    }

    public function addManualWithinStock(int $cartId, int $productId, int $qty): string
    {
        $db = $this->db();
        $db->beginTransaction();

        try {
            $productStmt = $db->prepare(
                "SELECT price, stock_quantity, status
                 FROM products
                 WHERE product_id = :product_id
                 FOR UPDATE"
            );
            $productStmt->execute([':product_id' => $productId]);
            $product = $productStmt->fetch();

            if (!$product || $product['status'] !== 'active') {
                $db->rollBack();
                return 'unavailable';
            }

            $itemStmt = $db->prepare(
                "SELECT cart_item_id, quantity
                 FROM cart_items
                 WHERE cart_id = :cart_id
                   AND product_id = :product_id
                   AND added_method = 'manual'
                   AND recipe_id IS NULL
                   AND recipe_ingredient_id IS NULL
                 LIMIT 1
                 FOR UPDATE"
            );
            $itemStmt->execute([
                ':cart_id' => $cartId,
                ':product_id' => $productId,
            ]);
            $item = $itemStmt->fetch();
            $newQuantity = (int) ($item['quantity'] ?? 0) + $qty;

            if ($newQuantity > (int) $product['stock_quantity']) {
                $db->rollBack();
                return 'insufficient_stock';
            }

            if ($item) {
                $updateStmt = $db->prepare(
                    "UPDATE cart_items
                     SET quantity = :quantity, unit_price = :unit_price
                     WHERE cart_item_id = :cart_item_id"
                );
                $updateStmt->execute([
                    ':quantity' => $newQuantity,
                    ':unit_price' => (float) $product['price'],
                    ':cart_item_id' => (int) $item['cart_item_id'],
                ]);
            } else {
                $insertStmt = $db->prepare(
                    "INSERT INTO cart_items
                        (cart_id, product_id, recipe_id, recipe_ingredient_id, quantity, unit_price, added_method)
                     VALUES
                        (:cart_id, :product_id, NULL, NULL, :quantity, :unit_price, 'manual')"
                );
                $insertStmt->execute([
                    ':cart_id' => $cartId,
                    ':product_id' => $productId,
                    ':quantity' => $qty,
                    ':unit_price' => (float) $product['price'],
                ]);
            }

            $db->commit();
            return 'added';
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
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
