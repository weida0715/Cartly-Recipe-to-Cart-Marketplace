<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Store extends Model
{
    protected string $table = 'stores';
    protected string $primaryKey = 'store_id';

    public function byUser(int $userId): ?array
    {
        $rows = $this->where('user_id', $userId);
        return $rows[0] ?? null;
    }

    public function approved(string $q = ''): array
    {
        if ($q === '') {
            return $this->query("SELECT * FROM stores WHERE store_status='approved'");
        }

        return $this->query(
            "SELECT * FROM stores
             WHERE store_status = 'approved'
               AND (store_name LIKE :q OR store_description LIKE :q OR store_address LIKE :q)",
            [':q' => '%' . $q . '%']
        );
    }

    public function findApproved(int $storeId): ?array
    {
        $rows = $this->query(
            "SELECT * FROM stores
             WHERE store_id = :id AND store_status='approved'
             LIMIT 1",
            [':id' => $storeId]
        );
        return $rows[0] ?? null;
    }

    public function statistics(int $storeId): array
    {
        $rows = $this->query(
            "SELECT
                (SELECT COUNT(*) FROM products WHERE store_id = :products_store) AS total_products,
                (SELECT COUNT(*) FROM products WHERE store_id = :active_store AND status = 'active') AS active_products,
                (SELECT COUNT(*) FROM merchant_orders WHERE store_id = :orders_store AND status <> 'cancelled') AS total_orders,
                (SELECT COUNT(*) FROM merchant_orders WHERE store_id = :completed_store AND status = 'completed') AS completed_orders,
                (SELECT COALESCE(SUM(subtotal - discount_amount), 0)
                 FROM merchant_orders
                 WHERE store_id = :revenue_store AND status <> 'cancelled') AS revenue",
            [
                ':products_store' => $storeId,
                ':active_store' => $storeId,
                ':orders_store' => $storeId,
                ':completed_store' => $storeId,
                ':revenue_store' => $storeId,
            ]
        );

        return $rows[0] ?? [
            'total_products' => 0,
            'active_products' => 0,
            'total_orders' => 0,
            'completed_orders' => 0,
            'revenue' => 0,
        ];
    }

    public function pending(): array
    {
        return $this->query(
            "SELECT s.*, u.username, u.full_name AS owner_name, u.email AS account_email, u.phone AS account_phone
             FROM stores s
             JOIN users u ON u.user_id = s.user_id
             WHERE s.store_status = 'pending'
             ORDER BY s.created_at ASC"
        );
    }

    public function approvedRequestHistory(): array
    {
        return $this->query(
            "SELECT s.*, u.username, u.full_name AS owner_name, u.email AS account_email,
                    COALESCE(s.reviewed_at, s.created_at) AS approved_at
             FROM stores s
             JOIN users u ON u.user_id = s.user_id
             WHERE s.store_status IN ('approved', 'closed')
             ORDER BY COALESCE(s.reviewed_at, s.created_at) DESC, s.store_id DESC"
        );
    }

    public function recordReview(int $storeId, string $status, string $adminNote): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE stores
             SET store_status = :status,
                 admin_note = :admin_note,
                 reviewed_at = CURRENT_TIMESTAMP
             WHERE store_id = :store_id"
        );

        return $stmt->execute([
            ':status' => $status,
            ':admin_note' => $adminNote,
            ':store_id' => $storeId,
        ]);
    }
}
