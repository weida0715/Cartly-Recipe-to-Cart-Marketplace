<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Report extends Model
{
    protected string $table = 'reports';
    protected string $primaryKey = 'report_id';

    public function pending(): array
    {
        return $this->query(
            "SELECT r.*, u.username AS reporter
             FROM reports r JOIN users u ON u.user_id = r.user_id
             WHERE r.status IN ('pending','reviewed') ORDER BY r.created_at DESC"
        );
    }

    public function createForUser(int $userId, string $targetType, int $targetId, string $reason): int
    {
        return $this->insert([
            'user_id' => $userId,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'reason' => $reason,
            'status' => 'pending',
        ]);
    }
}
