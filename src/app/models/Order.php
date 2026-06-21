<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Order extends Model
{
    protected string $table = 'orders';
    protected string $primaryKey = 'order_id';

    public function historyForUser(int $userId): array
    {
        (new MerchantOrder())->syncTimedStatusesForUser($userId);
        $orders = $this->query(
            "SELECT o.*, GROUP_CONCAT(mo.status) AS merchant_statuses
             FROM orders o
             LEFT JOIN merchant_orders mo ON mo.order_id = o.order_id
             WHERE o.user_id = :u
             GROUP BY o.order_id
             ORDER BY o.created_at DESC",
            [':u' => $userId]
        );
        return array_map(fn(array $order): array => $this->withDisplayStatus($order), $orders);
    }

    public function withDisplayStatus(array $order): array
    {
        $statuses = array_filter(explode(',', (string) ($order['merchant_statuses'] ?? '')));
        $order['display_order_status'] = $statuses ? $this->displayStatusFromMerchantStatuses($statuses) : $order['order_status'];
        return $order;
    }

    /** @param array<int, string> $statuses */
    public function displayStatusFromMerchantStatuses(array $statuses): string
    {
        $unique = array_values(array_unique($statuses));
        if (count($unique) === 1) {
            return $unique[0];
        }
        if (!array_diff($statuses, ['completed', 'cancelled'])) {
            return 'completed';
        }
        if (in_array('delivered', $statuses, true)) {
            return 'delivered';
        }
        if (in_array('out_for_delivery', $statuses, true)) {
            return 'out_for_delivery';
        }
        if (in_array('ready_to_deliver', $statuses, true)) {
            return 'ready_to_deliver';
        }
        if (in_array('preparing', $statuses, true)) {
            return 'preparing';
        }
        if (in_array('accepted', $statuses, true)) {
            return 'accepted';
        }
        if (in_array('completed', $statuses, true)) {
            return 'processing';
        }
        return 'pending';
    }
}
