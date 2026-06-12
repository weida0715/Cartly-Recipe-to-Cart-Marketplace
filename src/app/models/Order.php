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
        return $this->query(
            "SELECT * FROM orders WHERE user_id = :u ORDER BY created_at DESC",
            [':u' => $userId]
        );
    }
}
