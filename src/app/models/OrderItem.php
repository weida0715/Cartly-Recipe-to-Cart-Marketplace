<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class OrderItem extends Model
{
    protected string $table = 'order_items';
    protected string $primaryKey = 'order_item_id';

    public function forMerchantOrder(int $merchantOrderId): array
    {
        return $this->where('merchant_order_id', $merchantOrderId);
    }
}
