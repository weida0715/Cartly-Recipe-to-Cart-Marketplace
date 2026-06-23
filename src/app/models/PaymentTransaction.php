<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Model;

class PaymentTransaction extends Model
{
    protected string $table = 'payment_transactions';
    protected string $primaryKey = 'payment_transaction_id';

    public function forOrder(int $orderId): ?array
    {
        $rows = $this->query(
            'SELECT * FROM payment_transactions WHERE order_id = :order_id LIMIT 1',
            [':order_id' => $orderId]
        );
        return $rows[0] ?? null;
    }
}
