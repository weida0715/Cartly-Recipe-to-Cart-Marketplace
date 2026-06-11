<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class MerchantOrder extends Model
{
    protected string $table = 'merchant_orders';
    protected string $primaryKey = 'merchant_order_id';

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
}
