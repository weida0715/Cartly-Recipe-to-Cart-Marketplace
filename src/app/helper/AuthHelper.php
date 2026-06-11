<?php
declare(strict_types=1);

namespace App\Helpers;

class AuthHelper
{
    public static function check(): bool
    {
        return !empty($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user']['user_id']) ? (int) $_SESSION['user']['user_id'] : null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function login(array $user): void
    {
        $_SESSION['user'] = [
            'user_id'   => (int) $user['user_id'],
            'username'  => $user['username'],
            'full_name' => $user['full_name'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'status'    => $user['status'],
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Flash::set('error', 'Please login to continue.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();
        if (self::role() !== $role) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }
}
