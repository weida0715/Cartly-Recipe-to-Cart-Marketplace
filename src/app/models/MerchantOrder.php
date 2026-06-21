<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class MerchantOrder extends Model
{
    protected string $table = 'merchant_orders';
    protected string $primaryKey = 'merchant_order_id';
    public const STATUSES = ['pending', 'accepted', 'preparing', 'completed', 'cancelled'];

    public function forStore(int $storeId): array
    {
        return $this->query(
            "SELECT mo.*, o.created_at, o.user_id, u.username,
                    (SELECT COALESCE(SUM(quantity), 0) FROM order_items oi WHERE oi.merchant_order_id = mo.merchant_order_id) AS item_count
             FROM merchant_orders mo
             JOIN orders o ON o.order_id = mo.order_id
             JOIN users u  ON u.user_id  = o.user_id
             WHERE mo.store_id = :s
             ORDER BY o.created_at DESC",
            [':s' => $storeId]
        );
    }

    public function forOrder(int $orderId): array
    {
        return $this->query(
            "SELECT mo.*, s.store_name
             FROM merchant_orders mo
             JOIN stores s ON s.store_id = mo.store_id
             WHERE mo.order_id = :o",
            [':o' => $orderId]
        );
    }

    public function updateStatusAndSyncParent(int $merchantOrderId, string $status): bool
    {
        $row = $this->find($merchantOrderId);
        if (!$row || !in_array($status, self::STATUSES, true)) {
            return false;
        }

        $db = $this->db();
        try {
            $db->beginTransaction();
            $updated = $this->update($merchantOrderId, ['status' => $status]);
            if (!$updated) {
                $db->rollBack();
                return false;
            }
            $this->syncParentStatus((int) $row['order_id']);
            $db->commit();
            return true;
        } catch (\Throwable) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return false;
        }
    }

    private function syncParentStatus(int $orderId): void
    {
        $rows = $this->query('SELECT status FROM merchant_orders WHERE order_id = :o', [':o' => $orderId]);
        $statuses = array_column($rows, 'status');
        if (!$statuses) {
            return;
        }

        $uniqueStatuses = array_unique($statuses);
        $orderStatus = 'pending';
        if (count($uniqueStatuses) === 1 && reset($uniqueStatuses) === 'cancelled') {
            $orderStatus = 'cancelled';
        } elseif (!array_diff($statuses, ['completed', 'cancelled'])) {
            $orderStatus = 'completed';
        } elseif (array_intersect($statuses, ['accepted', 'preparing', 'completed'])) {
            $orderStatus = 'processing';
        }

        $stmt = $this->db()->prepare('UPDATE orders SET order_status = :s WHERE order_id = :o');
        $stmt->execute([':s' => $orderStatus, ':o' => $orderId]);
    }
}
