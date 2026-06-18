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
            "SELECT mo.*, o.created_at, o.user_id, u.username
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

        $updated = $this->update($merchantOrderId, ['status' => $status]);
        if ($updated) {
            $this->syncParentStatus((int) $row['order_id']);
        }
        return $updated;
    }

    private function syncParentStatus(int $orderId): void
    {
        $rows = $this->query('SELECT status FROM merchant_orders WHERE order_id = :o', [':o' => $orderId]);
        $statuses = array_column($rows, 'status');
        if (!$statuses) {
            return;
        }

        $orderStatus = 'pending';
        if (count(array_unique($statuses)) === 1 && $statuses[0] === 'cancelled') {
            $orderStatus = 'cancelled';
        } elseif (count(array_unique($statuses)) === 1 && $statuses[0] === 'completed') {
            $orderStatus = 'completed';
        } elseif (array_intersect($statuses, ['accepted', 'preparing', 'completed'])) {
            $orderStatus = 'processing';
        }

        $stmt = $this->db()->prepare('UPDATE orders SET order_status = :s WHERE order_id = :o');
        $stmt->execute([':s' => $orderStatus, ':o' => $orderId]);
    }
}
