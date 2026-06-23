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

    public function approved(string $q = ''): array
    {
        if ($q === '') {
            return $this->query("SELECT * FROM stores WHERE store_status='approved'");
        }

        return $this->query(
            "SELECT * FROM stores
             WHERE store_status = 'approved'
               AND (store_name LIKE :q OR store_description LIKE :q OR store_address LIKE :q)",
            [':q' => '%' . $q . '%']
        );
    }

    public function findApproved(int $storeId): ?array
    {
        $rows = $this->query(
            "SELECT * FROM stores
             WHERE store_id = :id AND store_status='approved'
             LIMIT 1",
            [':id' => $storeId]
        );
        return $rows[0] ?? null;
    }

    public function pending(): array
    {
        return $this->query(
            "SELECT s.*, u.username, u.full_name AS owner_name, u.email AS account_email, u.phone AS account_phone
             FROM stores s
             JOIN users u ON u.user_id = s.user_id
             WHERE s.store_status = 'pending'
             ORDER BY s.created_at ASC"
        );
    }

    public function approvedRequestHistory(): array
    {
        return $this->query(
            "SELECT s.*, u.username, u.full_name AS owner_name, u.email AS account_email,
                    COALESCE(s.reviewed_at, s.created_at) AS approved_at
             FROM stores s
             JOIN users u ON u.user_id = s.user_id
             WHERE s.store_status IN ('approved', 'closed')
             ORDER BY COALESCE(s.reviewed_at, s.created_at) DESC, s.store_id DESC"
        );
    }
}
