<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';
require_once __DIR__ . '/../src/app/helper/Model.php';

use App\Models\CartItem;

class CartItemTest extends TestCase
{
    public function test_count_for_user_returns_server_side_cart_line_count(): void
    {
        $cartItem = new class extends CartItem {
            public array $lastQuery = [];

            public function query(string $sql, array $params = []): array
            {
                $this->lastQuery = ['sql' => $sql, 'params' => $params];
                return [['item_count' => 3]];
            }
        };

        $this->assertSame(3, $cartItem->countForUser(12));
        $this->assertStringContainsString('COUNT(ci.cart_item_id)', $cartItem->lastQuery['sql']);
        $this->assertStringContainsString('WHERE c.user_id = :user_id', $cartItem->lastQuery['sql']);
        $this->assertSame([':user_id' => 12], $cartItem->lastQuery['params']);
    }

    public function test_count_for_user_defaults_to_zero(): void
    {
        $cartItem = new class extends CartItem {
            public function query(string $sql, array $params = []): array
            {
                return [];
            }
        };

        $this->assertSame(0, $cartItem->countForUser(12));
    }

    public function test_manual_cart_add_uses_a_transaction_and_row_locks(): void
    {
        $method = new ReflectionMethod(CartItem::class, 'addManualWithinStock');
        $source = file(__DIR__ . '/../src/app/models/CartItem.php');
        $body = implode('', array_slice(
            $source,
            $method->getStartLine() - 1,
            $method->getEndLine() - $method->getStartLine() + 1
        ));

        $this->assertStringContainsString('beginTransaction()', $body);
        $this->assertGreaterThanOrEqual(2, substr_count($body, 'FOR UPDATE'));
        $this->assertStringContainsString('rollBack()', $body);
        $this->assertStringContainsString('commit()', $body);
    }
}
