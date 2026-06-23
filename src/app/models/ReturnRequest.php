<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Model;

class ReturnRequest extends Model
{
    protected string $table = 'return_requests';
    protected string $primaryKey = 'return_request_id';

    public function eligibleItemForCustomer(int $orderItemId, int $userId): ?array
    {
        $rows = $this->query(
            "SELECT oi.*, mo.order_id, mo.store_id, mo.status AS merchant_order_status, s.store_name,
                    rr.return_request_id
             FROM order_items oi
             JOIN merchant_orders mo ON mo.merchant_order_id = oi.merchant_order_id
             JOIN orders o ON o.order_id = mo.order_id
             JOIN stores s ON s.store_id = mo.store_id
             LEFT JOIN return_requests rr ON rr.order_item_id = oi.order_item_id
             WHERE oi.order_item_id = :item_id AND o.user_id = :user_id
             LIMIT 1",
            [':item_id' => $orderItemId, ':user_id' => $userId]
        );
        return $rows[0] ?? null;
    }

    public function forMerchantOrder(int $merchantOrderId): array
    {
        return $this->query(
            "SELECT rr.*, oi.product_name_snapshot, oi.unit_price, oi.quantity AS ordered_quantity
             FROM return_requests rr
             JOIN order_items oi ON oi.order_item_id = rr.order_item_id
             WHERE rr.merchant_order_id = :merchant_order_id
             ORDER BY rr.created_at DESC",
            [':merchant_order_id' => $merchantOrderId]
        );
    }

    public function findForCustomer(int $requestId, int $userId): ?array
    {
        return $this->findWithContext($requestId, 'rr.user_id = :owner_id', $userId);
    }

    public function findForStore(int $requestId, int $storeId): ?array
    {
        return $this->findWithContext($requestId, 'rr.store_id = :owner_id', $storeId);
    }

    public function createRequest(array $data): int
    {
        return $this->insert($data);
    }

    public function decide(int $requestId, string $status, float $refundAmount, string $merchantNote): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE return_requests
             SET status = :status, refund_amount = :refund_amount, merchant_note = :merchant_note,
                 decided_at = CURRENT_TIMESTAMP,
                 resolved_at = CASE WHEN :resolved = 1 THEN CURRENT_TIMESTAMP ELSE NULL END
             WHERE return_request_id = :id AND status = 'pending'"
        );
        $ok = $stmt->execute([
            ':status' => $status,
            ':refund_amount' => $refundAmount > 0 ? $refundAmount : null,
            ':merchant_note' => $merchantNote,
            ':resolved' => in_array($status, ['refunded', 'rejected'], true) ? 1 : 0,
            ':id' => $requestId,
        ]);
        $updated = $ok && $stmt->rowCount() === 1;
        if ($updated && $status === 'refunded') {
            $this->syncOrderPaymentStatus($requestId);
        }
        return $updated;
    }

    public function markReturnShipped(int $requestId, int $userId): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE return_requests
             SET status = 'return_shipped', return_shipped_at = CURRENT_TIMESTAMP
             WHERE return_request_id = :id AND user_id = :user_id AND status = 'return_approved'"
        );
        $stmt->execute([':id' => $requestId, ':user_id' => $userId]);
        return $stmt->rowCount() === 1;
    }

    public function completeReturn(int $requestId, int $storeId): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE return_requests
             SET status = 'refunded', resolved_at = CURRENT_TIMESTAMP
             WHERE return_request_id = :id AND store_id = :store_id AND status = 'return_shipped'"
        );
        $stmt->execute([':id' => $requestId, ':store_id' => $storeId]);
        if ($stmt->rowCount() !== 1) {
            return false;
        }
        $this->syncOrderPaymentStatus($requestId);
        return true;
    }

    private function findWithContext(int $requestId, string $ownerWhere, int $ownerId): ?array
    {
        $rows = $this->query(
            "SELECT rr.*, oi.product_name_snapshot, oi.unit_price, oi.quantity AS ordered_quantity,
                    mo.order_id, o.total_amount, o.payment_status, s.store_name, s.user_id AS merchant_user_id
             FROM return_requests rr
             JOIN order_items oi ON oi.order_item_id = rr.order_item_id
             JOIN merchant_orders mo ON mo.merchant_order_id = rr.merchant_order_id
             JOIN orders o ON o.order_id = mo.order_id
             JOIN stores s ON s.store_id = rr.store_id
             WHERE rr.return_request_id = :id AND {$ownerWhere}
             LIMIT 1",
            [':id' => $requestId, ':owner_id' => $ownerId]
        );
        return $rows[0] ?? null;
    }

    private function syncOrderPaymentStatus(int $requestId): void
    {
        $db = $this->db();
        try {
            $db->beginTransaction();
            $orderStmt = $db->prepare(
                "SELECT o.order_id, o.total_amount
                 FROM return_requests rr
                 JOIN merchant_orders mo ON mo.merchant_order_id = rr.merchant_order_id
                 JOIN orders o ON o.order_id = mo.order_id
                 WHERE rr.return_request_id = :id
                 FOR UPDATE"
            );
            $orderStmt->execute([':id' => $requestId]);
            $order = $orderStmt->fetch();
            if (!$order) {
                $db->rollBack();
                return;
            }

            $sumStmt = $db->prepare(
                "SELECT COALESCE(SUM(rr.refund_amount), 0) AS refunded_total
                 FROM merchant_orders mo
                 JOIN return_requests rr ON rr.merchant_order_id = mo.merchant_order_id
                    AND rr.status = 'refunded'
                 WHERE mo.order_id = :order_id"
            );
            $sumStmt->execute([':order_id' => (int) $order['order_id']]);
            $sumRow = $sumStmt->fetch();
            $status = (float) ($sumRow['refunded_total'] ?? 0) >= (float) $order['total_amount']
                ? 'refunded'
                : 'partially_refunded';

            $updateStmt = $db->prepare('UPDATE orders SET payment_status = :status WHERE order_id = :order_id');
            $updateStmt->execute([':status' => $status, ':order_id' => (int) $order['order_id']]);
            $db->commit();
        } catch (\Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $error;
        }
    }
}
