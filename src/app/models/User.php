<?php
declare(strict_types=1);
namespace App\Models;

use App\Helpers\Model;

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'user_id';

    public function allNonAdmins(string $order = 'created_at DESC'): array
    {
        $allowedOrders = ['created_at DESC', 'created_at ASC'];
        if ($order !== '' && !in_array($order, $allowedOrders, true)) {
            $order = 'created_at DESC';
        }

        $sql = 'SELECT * FROM users WHERE role <> :role';
        if ($order !== '') {
            $sql .= " ORDER BY {$order}";
        }

        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':role' => 'admin']);
        return $stmt->fetchAll();
    }

    public function findByLogin(string $login): ?array
    {
        $stmt = $this->db()->prepare(
            'SELECT * FROM users WHERE email = :email OR username = :username LIMIT 1'
        );
        $stmt->execute([':email' => $login, ':username' => $login]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM users WHERE email = :e LIMIT 1');
        $stmt->execute([':e' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function existsByEmailOrUsername(string $email, string $username): bool
    {
        $stmt = $this->db()->prepare('SELECT 1 FROM users WHERE email = :e OR username = :u LIMIT 1');
        $stmt->execute([':e' => $email, ':u' => $username]);
        return (bool) $stmt->fetchColumn();
    }

    public function storeResetToken(int $userId, string $token, string $expiresAt): void
    {
        $this->update($userId, [
            'reset_token_hash' => hash('sha256', $token),
            'reset_token_expires_at' => $expiresAt,
        ]);
    }

    public function findByValidResetToken(string $token): ?array
    {
        $stmt = $this->db()->prepare(
            'SELECT * FROM users WHERE reset_token_hash = :h AND reset_token_expires_at > NOW() LIMIT 1'
        );
        $stmt->execute([':h' => hash('sha256', $token)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updatePassword(int $userId, string $password): void
    {
        $this->update($userId, [
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'reset_token_hash' => null,
            'reset_token_expires_at' => null,
        ]);
    }

    public function emailOrUsernameTakenByAnother(string $email, string $username, int $userId): bool
    {
        $stmt = $this->db()->prepare(
            'SELECT 1 FROM users WHERE (email = :e OR username = :u) AND user_id <> :id LIMIT 1'
        );
        $stmt->execute([':e' => $email, ':u' => $username, ':id' => $userId]);
        return (bool) $stmt->fetchColumn();
    }
}
