<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class MerchantOrder extends Model
{
    protected string $table = 'merchant_orders';
    protected string $primaryKey = 'merchant_order_id';
    public const STATUSES = ['pending', 'accepted', 'preparing', 'ready_to_deliver', 'out_for_delivery', 'delivered', 'completed', 'cancelled'];
    private const STATUS_TIMESTAMPS = [
        'accepted' => 'accepted_at',
        'preparing' => 'preparing_at',
        'ready_to_deliver' => 'ready_to_deliver_at',
        'out_for_delivery' => 'out_for_delivery_at',
        'delivered' => 'delivered_at',
        'completed' => 'completed_at',
        'cancelled' => 'cancelled_at',
    ];

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
            "SELECT mo.*, s.store_name, o.created_at AS order_created_at
             FROM merchant_orders mo
             JOIN orders o ON o.order_id = mo.order_id
             JOIN stores s ON s.store_id = mo.store_id
             WHERE mo.order_id = :o",
            [':o' => $orderId]
        );
    }

    public function updateStatusAndSyncParent(int $merchantOrderId, string $status): bool
    {
        return $this->transitionStatus($merchantOrderId, null, $status);
    }

    public function syncTimedStatusesForOrder(int $orderId): void
    {
        foreach ($this->forOrder($orderId) as $row) {
            $this->syncTimedStatusRow($row);
        }
    }

    public function syncTimedStatusesForUser(int $userId): void
    {
        $rows = $this->query(
            "SELECT mo.*, o.created_at AS order_created_at
             FROM merchant_orders mo
             JOIN orders o ON o.order_id = mo.order_id
             WHERE o.user_id = :u
               AND mo.status IN ('ready_to_deliver', 'out_for_delivery')",
            [':u' => $userId]
        );
        foreach ($rows as $row) {
            $this->syncTimedStatusRow($row);
        }
    }

    public function syncTimedStatusesForStore(int $storeId): void
    {
        $rows = $this->query(
            "SELECT mo.*, o.created_at AS order_created_at
             FROM merchant_orders mo
             JOIN orders o ON o.order_id = mo.order_id
             WHERE mo.store_id = :s
               AND mo.status IN ('ready_to_deliver', 'out_for_delivery')",
            [':s' => $storeId]
        );
        foreach ($rows as $row) {
            $this->syncTimedStatusRow($row);
        }
    }

    public function updateStatusIfCurrentAndSyncParent(int $merchantOrderId, array $fromStatuses, string $status): bool
    {
        if (!in_array($status, self::STATUSES, true)) {
            return false;
        }

        return $this->transitionStatus($merchantOrderId, $fromStatuses, $status);
    }

    public function belongsToCustomer(int $merchantOrderId, int $userId): ?array
    {
        $rows = $this->query(
            "SELECT mo.*, o.created_at AS order_created_at
             FROM merchant_orders mo
             JOIN orders o ON o.order_id = mo.order_id
             WHERE mo.merchant_order_id = :mo AND o.user_id = :u
             LIMIT 1",
            [':mo' => $merchantOrderId, ':u' => $userId]
        );
        return $rows ? $rows[0] : null;
    }

    public function belongsToStore(int $merchantOrderId, int $storeId): ?array
    {
        $rows = $this->query(
            "SELECT mo.*, o.created_at AS order_created_at
             FROM merchant_orders mo
             JOIN orders o ON o.order_id = mo.order_id
             WHERE mo.merchant_order_id = :mo AND mo.store_id = :s
             LIMIT 1",
            [':mo' => $merchantOrderId, ':s' => $storeId]
        );
        return $rows ? $rows[0] : null;
    }

    private function persistStatus(int $merchantOrderId, string $status, ?string $timestamp = null, array $extraColumns = []): bool
    {
        $timestamp ??= date('Y-m-d H:i:s');
        $data = ['status' => $status];
        if (isset(self::STATUS_TIMESTAMPS[$status])) {
            $data[self::STATUS_TIMESTAMPS[$status]] = $timestamp;
        }
        foreach ($extraColumns as $column => $value) {
            $data[$column] = $value;
        }
        return $this->update($merchantOrderId, $data);
    }

    private function transitionStatus(int $merchantOrderId, ?array $fromStatuses, string $status): bool
    {
        if (!in_array($status, self::STATUSES, true)) {
            return false;
        }

        $db = $this->db();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare('SELECT * FROM merchant_orders WHERE merchant_order_id = :id FOR UPDATE');
            $stmt->execute([':id' => $merchantOrderId]);
            $row = $stmt->fetch();
            if (!$row) {
                $db->rollBack();
                return false;
            }
            $currentStatus = (string) $row['status'];
            if ($currentStatus === $status || ($fromStatuses !== null && !in_array($currentStatus, $fromStatuses, true))) {
                $db->rollBack();
                return false;
            }

            if (!$this->persistStatus($merchantOrderId, $status)) {
                $db->rollBack();
                return false;
            }
            if ($status === 'cancelled') {
                $this->restoreCancelledStock($merchantOrderId);
            }
            $this->syncParentStatus((int) $row['order_id']);
            $db->commit();
            $this->notifyCustomerOfStatus((int) $row['order_id'], $merchantOrderId, $status);
            return true;
        } catch (\Throwable) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return false;
        }
    }

    private function syncTimedStatusRow(array $row): void
    {
        $status = (string) ($row['status'] ?? 'pending');
        $id = (int) ($row['merchant_order_id'] ?? 0);
        if ($id <= 0) {
            return;
        }

        if ($status === 'ready_to_deliver') {
            $readyAt = strtotime((string) ($row['ready_to_deliver_at'] ?? ''));
            if ($readyAt !== false) {
                $now = time();
                if ($now - $readyAt >= 20) {
                    $this->updateStatusAndSyncParentWithTimestamps(
                        $id,
                        'delivered',
                        date('Y-m-d H:i:s', $readyAt + 20),
                        ['out_for_delivery_at' => date('Y-m-d H:i:s', $readyAt + 10)]
                    );
                    return;
                }
                if ($now - $readyAt >= 10) {
                    $this->updateStatusAndSyncParentWithTimestamps(
                        $id,
                        'out_for_delivery',
                        date('Y-m-d H:i:s', $readyAt + 10)
                    );
                    return;
                }
            }
        }

        if ($status === 'out_for_delivery') {
            $outAt = strtotime((string) ($row['out_for_delivery_at'] ?? ''));
            if ($outAt !== false && time() - $outAt >= 10) {
                $this->updateStatusAndSyncParentWithTimestamps(
                    $id,
                    'delivered',
                    date('Y-m-d H:i:s', $outAt + 10)
                );
            }
        }
    }

    private function updateStatusAndSyncParentWithTimestamps(int $merchantOrderId, string $status, string $timestamp, array $extraColumns = []): bool
    {
        $row = $this->find($merchantOrderId);
        if (!$row || !in_array($status, self::STATUSES, true)) {
            return false;
        }

        $db = $this->db();
        try {
            $db->beginTransaction();
            $updated = $this->persistStatus($merchantOrderId, $status, $timestamp, $extraColumns);
            $fresh = $this->find($merchantOrderId);
            if (!$updated || !$fresh || (string) $fresh['status'] !== $status) {
                $db->rollBack();
                return false;
            }
            $this->syncParentStatus((int) $row['order_id']);
            $db->commit();
            $this->notifyCustomerOfStatus((int) $row['order_id'], $merchantOrderId, $status);
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
        } elseif (array_intersect($statuses, ['accepted', 'preparing', 'ready_to_deliver', 'out_for_delivery', 'delivered', 'completed'])) {
            $orderStatus = 'processing';
        }

        $stmt = $this->db()->prepare('UPDATE orders SET order_status = :s WHERE order_id = :o');
        $stmt->execute([':s' => $orderStatus, ':o' => $orderId]);
    }

    private function restoreCancelledStock(int $merchantOrderId): void
    {
        $stmt = $this->db()->prepare(
            "UPDATE products p
             JOIN order_items oi ON oi.product_id = p.product_id
             SET p.stock_quantity = p.stock_quantity + oi.quantity,
                 p.status = CASE WHEN p.status = 'out_of_stock' THEN 'active' ELSE p.status END
             WHERE oi.merchant_order_id = :merchant_order_id"
        );
        $stmt->execute([':merchant_order_id' => $merchantOrderId]);
    }

    private function notifyCustomerOfStatus(int $orderId, int $merchantOrderId, string $status): void
    {
        $rows = $this->query('SELECT user_id FROM orders WHERE order_id = :order_id LIMIT 1', [':order_id' => $orderId]);
        if (!$rows) {
            return;
        }
        $labels = [
            'accepted' => 'accepted by the merchant',
            'preparing' => 'being prepared',
            'ready_to_deliver' => 'ready for delivery',
            'out_for_delivery' => 'out for delivery',
            'delivered' => 'delivered',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
        ];
        if (!isset($labels[$status])) {
            return;
        }
        try {
            (new Notification())->createForUser(
                (int) $rows[0]['user_id'],
                $status === 'cancelled' ? 'warning' : 'info',
                'Order status updated',
                'Store order #' . $merchantOrderId . ' is now ' . $labels[$status] . '.',
                '/orders/' . $orderId
            );
        } catch (\Throwable) {
            // Order updates remain available before the notification migration is applied.
        }
    }
}
