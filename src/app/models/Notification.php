<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Model;

class Notification extends Model
{
    protected string $table = 'notifications';
    protected string $primaryKey = 'notification_id';

    public function createForUser(int $userId, string $type, string $title, string $message, ?string $actionUrl = null): int
    {
        if ($userId <= 0 || trim($message) === '') {
            return 0;
        }

        return $this->insert([
            'user_id' => $userId,
            'type' => $this->normalizeType($type),
            'title' => $this->truncate(trim($title) ?: 'Cartly update', 150),
            'message' => trim($message),
            'action_url' => $this->normalizeActionUrl($actionUrl),
            'is_read' => 0,
        ]);
    }

    public function createForRole(string $role, string $type, string $title, string $message, ?string $actionUrl = null): void
    {
        $rows = $this->query(
            "SELECT user_id FROM users WHERE role = :role AND status = 'active'",
            [':role' => $role]
        );
        foreach ($rows as $row) {
            $this->createForUser((int) $row['user_id'], $type, $title, $message, $actionUrl);
        }
    }

    public function createForStore(int $storeId, string $type, string $title, string $message, ?string $actionUrl = null): void
    {
        $rows = $this->query('SELECT user_id FROM stores WHERE store_id = :store_id LIMIT 1', [':store_id' => $storeId]);
        if ($rows) {
            $this->createForUser((int) $rows[0]['user_id'], $type, $title, $message, $actionUrl);
        }
    }

    public function latestForUser(int $userId, int $limit = 8): array
    {
        $limit = max(1, min(50, $limit));
        return $this->query(
            "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC, notification_id DESC LIMIT {$limit}",
            [':user_id' => $userId]
        );
    }

    public function allForUser(int $userId): array
    {
        return $this->query(
            'SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC, notification_id DESC',
            [':user_id' => $userId]
        );
    }

    public function unreadCount(int $userId): int
    {
        $rows = $this->query(
            'SELECT COUNT(*) AS total FROM notifications WHERE user_id = :user_id AND is_read = 0',
            [':user_id' => $userId]
        );
        return (int) ($rows[0]['total'] ?? 0);
    }

    public function findForUser(int $notificationId, int $userId): ?array
    {
        $rows = $this->query(
            'SELECT * FROM notifications WHERE notification_id = :id AND user_id = :user_id LIMIT 1',
            [':id' => $notificationId, ':user_id' => $userId]
        );
        return $rows[0] ?? null;
    }

    public function markRead(int $notificationId, int $userId): void
    {
        $stmt = $this->db()->prepare(
            'UPDATE notifications SET is_read = 1 WHERE notification_id = :id AND user_id = :user_id'
        );
        $stmt->execute([':id' => $notificationId, ':user_id' => $userId]);
    }

    public function markAllRead(int $userId): void
    {
        $stmt = $this->db()->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = :user_id');
        $stmt->execute([':user_id' => $userId]);
    }

    private function normalizeType(string $type): string
    {
        return in_array($type, ['info', 'success', 'warning', 'error'], true) ? $type : 'info';
    }

    private function normalizeActionUrl(?string $actionUrl): ?string
    {
        $actionUrl = trim((string) $actionUrl);
        return $actionUrl !== '' && str_starts_with($actionUrl, '/') && !str_starts_with($actionUrl, '//')
            ? $this->truncate($actionUrl, 255)
            : null;
    }

    private function truncate(string $value, int $length): string
    {
        return function_exists('mb_substr')
            ? mb_substr($value, 0, $length, 'UTF-8')
            : substr($value, 0, $length);
    }
}
