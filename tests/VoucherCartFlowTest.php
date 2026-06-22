<?php
declare(strict_types=1);

use App\Helpers\CartVoucherSession;
use App\Helpers\VoucherDateValidator;
use App\Models\Voucher;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}

class VoucherCartFlowTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function test_cart_vouchers_are_scoped_normalized_and_removable(): void
    {
        $this->assertTrue(CartVoucherSession::add(4, 9, 2, ' save10 '));
        $this->assertFalse(CartVoucherSession::add(4, 9, 2, 'SAVE10'));
        $this->assertSame([2 => ['SAVE10']], CartVoucherSession::all(4, 9));
        $this->assertSame([], CartVoucherSession::all(5, 9));
        $this->assertSame([], CartVoucherSession::all(4, 10));

        CartVoucherSession::remove(4, 9, 2, 'save10');
        $this->assertSame([], CartVoucherSession::all(4, 9));
    }

    public function test_multiple_vouchers_are_applied_in_order_to_the_remaining_total(): void
    {
        $voucherModel = new class extends Voucher {
            private array $vouchers = [
                'FIXED10' => [
                    'voucher_id' => 1,
                    'voucher_code' => 'FIXED10',
                    'discount_type' => 'fixed',
                    'discount_value' => 10,
                ],
                'HALF' => [
                    'voucher_id' => 2,
                    'voucher_code' => 'HALF',
                    'discount_type' => 'percentage',
                    'discount_value' => 50,
                ],
            ];

            public function findValidForStore(string $code, int $storeId, float $subtotal): ?array
            {
                return $storeId === 3 && $subtotal >= 100
                    ? ($this->vouchers[$code] ?? null)
                    : null;
            }
        };

        $pricing = $voucherModel->resolveCodesForStore(['fixed10', 'HALF'], 3, 100);

        $this->assertSame(['FIXED10', 'HALF'], array_column($pricing['applied'], 'voucher_code'));
        $this->assertSame(55.0, $pricing['discount_total']);
        $this->assertSame(45.0, $pricing['final_total']);
    }

    public function test_voucher_date_range_requires_exact_dates_in_order(): void
    {
        $this->assertNull(VoucherDateValidator::error('2026-06-22', '2026-06-22', false));
        $this->assertNull(VoucherDateValidator::error(null, null, true));
        $this->assertSame(
            'End date must be after or equal to start date.',
            VoucherDateValidator::error('2026-06-23', '2026-06-22', false)
        );
        $this->assertSame(
            'Voucher dates are invalid.',
            VoucherDateValidator::error('2026-02-30', '2026-03-01', false)
        );
    }

    public function test_cart_renders_apply_and_remove_voucher_actions(): void
    {
        $html = $this->render('order/cart.php', [
            'count' => 1,
            'groups' => [7 => [
                'store_name' => 'Market',
                'items' => [[
                    'cart_item_id' => 2,
                    'product_name' => 'Rice',
                    'quantity' => 1,
                    'stock_quantity' => 5,
                    'line_total' => 20,
                    'added_method' => 'manual',
                ]],
                'subtotal' => 20,
                'discount_total' => 5,
                'final_total' => 15,
                'available_vouchers' => [],
                'applied_vouchers' => [[
                    'voucher_code' => 'SAVE5',
                    'discount_amount' => 5,
                ]],
            ]],
            'subtotal' => 20,
            'discountTotal' => 5,
            'total' => 15,
        ]);

        $this->assertStringContainsString('/cart/vouchers/apply', $html);
        $this->assertStringContainsString('/cart/vouchers/remove', $html);
        $this->assertStringContainsString('SAVE5', $html);
        $this->assertStringContainsString('RM 15.00', $html);
    }

    public function test_checkout_displays_applied_vouchers_without_an_input(): void
    {
        $html = $this->render('order/checkout.php', [
            'user' => ['full_name' => 'Customer'],
            'groups' => [[
                'store_name' => 'Market',
                'items' => [['product_name' => 'Rice', 'quantity' => 1, 'line_total' => 20]],
                'subtotal' => 20,
                'discount_total' => 5,
                'final_total' => 15,
                'applied_vouchers' => [['voucher_code' => 'SAVE5', 'discount_amount' => 5]],
            ]],
            'subtotal' => 20,
            'discountTotal' => 5,
            'total' => 15,
        ]);

        $this->assertStringContainsString('Voucher SAVE5', $html);
        $this->assertStringContainsString('Edit cart vouchers', $html);
        $this->assertStringNotContainsString('name="voucher[', $html);
    }

    public function test_merchant_voucher_form_links_start_and_end_dates(): void
    {
        $html = $this->render('merchant/vouchers.php', ['vouchers' => []]);

        $this->assertStringContainsString('data-voucher-start', $html);
        $this->assertStringContainsString('data-voucher-end', $html);
        $this->assertStringContainsString('setCustomValidity', $html);
    }

    private function render(string $view, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require __DIR__ . '/../src/app/views/' . $view;
        return (string) ob_get_clean();
    }
}
