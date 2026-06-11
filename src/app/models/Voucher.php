<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Voucher extends Model
{
    protected string $table = 'vouchers';
    protected string $primaryKey = 'voucher_id';

    public function byStore(int $storeId): array
    {
        return $this->where('store_id', $storeId, 'voucher_id DESC');
    }

    public function findValidForStore(string $code, int $storeId, float $subtotal): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT * FROM vouchers
             WHERE voucher_code = :c AND store_id = :s AND status='active'
               AND (start_date IS NULL OR start_date <= CURDATE())
               AND (end_date   IS NULL OR end_date   >= CURDATE())
               AND (usage_limit = 0 OR used_count < usage_limit)
               AND minimum_spend <= :sub
             LIMIT 1"
        );
        $stmt->execute([':c' => $code, ':s' => $storeId, ':sub' => $subtotal]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function computeDiscount(array $voucher, float $subtotal): float
    {
        if ($voucher['discount_type'] === 'fixed') {
            return min($subtotal, (float) $voucher['discount_value']);
        }
        return round($subtotal * ((float) $voucher['discount_value'] / 100), 2);
    }

    public function increment(int $voucherId): void
    {
        $this->db()->prepare('UPDATE vouchers SET used_count = used_count + 1 WHERE voucher_id = :v')
            ->execute([':v' => $voucherId]);
    }
}
