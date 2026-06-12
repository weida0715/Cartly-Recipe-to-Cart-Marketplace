<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Cart extends Model
{
    protected string $table = 'carts';
    protected string $primaryKey = 'cart_id';

    public function findOrCreateForUser(int $userId): array
    {
        $rows = $this->where('user_id', $userId);
        if ($rows) return $rows[0];
        $id = $this->insert(['user_id' => $userId]);
        return $this->find($id);
    }

    public function clear(int $cartId): void
    {
        $this->db()->prepare('DELETE FROM cart_items WHERE cart_id = :c')->execute([':c' => $cartId]);
    }
}
