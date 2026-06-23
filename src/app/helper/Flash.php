<?php
declare(strict_types=1);

namespace App\Helpers;

class Flash
{
    public static function set(string $type, string $message, bool $persistNotification = false): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
        if (!$persistNotification || empty($_SESSION['user']['user_id'])) {
            return;
        }

        try {
            (new \App\Models\Notification())->createForUser(
                (int) $_SESSION['user']['user_id'],
                $type,
                match ($type) {
                    'success' => 'Action completed',
                    'warning' => 'Action required',
                    'error' => 'Something went wrong',
                    default => 'Cartly update',
                },
                $message
            );
        } catch (\Throwable) {
            // Flash messages must still work before the notification migration is applied.
        }
    }

    /** @return array<int, array{type:string, message:string}> */
    public static function pull(): array
    {
        $items = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $items;
    }
}
