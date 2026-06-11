<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Store extends Model
{
    protected string $table = 'stores';
    protected string $primaryKey = 'store_id';

    public function byUser(int $userId): ?array
    {
        $rows = $this->where('user_id', $userId);
        return $rows[0] ?? null;
    }

    public function approved(): array
    {
        return $this->query("SELECT * FROM stores WHERE store_status='approved'");
    }

    public function pending(): array
    {
        return $this->query("SELECT s.*, u.username, u.email FROM stores s JOIN users u ON u.user_id=s.user_id WHERE s.store_status='pending' ORDER BY s.created_at DESC");
    }
}
