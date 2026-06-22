<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', '/uploads');
}

class ViewNullSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function test_admin_dashboard_accepts_missing_stats(): void
    {
        $html = $this->render('admin/dashboard.php', ['stats' => null]);

        $this->assertStringContainsString('Admin dashboard', $html);
        $this->assertStringContainsString('Users', $html);
    }

    public function test_customer_dashboard_accepts_partial_request_order_and_recipe_data(): void
    {
        $html = $this->render('customer/dashboard.php', [
            'storeRequest' => ['admin_note' => null],
            'orders' => [[]],
            'recipes' => [[]],
        ]);

        $this->assertStringContainsString('pending', $html);
        $this->assertStringContainsString('Unnamed store', $html);
        $this->assertStringContainsString('Untitled recipe', $html);
    }

    public function test_merchant_dashboard_accepts_missing_store(): void
    {
        $html = $this->render('merchant/dashboard.php', [
            'store' => null,
            'totals' => [],
            'revenueChange' => null,
            'salesChart' => [],
            'orderTrendChart' => [],
            'orders' => [],
        ]);

        $this->assertStringContainsString('Welcome, Merchant', $html);
    }

    public function test_recipe_filters_accept_null_search_query_without_showing_reset(): void
    {
        $html = $this->render('recipe/index.php', [
            'q' => null,
            'cuisine' => null,
            'difficulty' => null,
            'sort' => null,
            'total' => 0,
            'page' => 1,
            'pages' => 1,
            'recipes' => [],
        ]);

        $this->assertStringContainsString('value=""', $html);
        $this->assertStringNotContainsString('>Reset</a>', $html);
    }

    public function test_cart_order_summary_shows_subtotal_delivery_and_total(): void
    {
        $html = $this->render('order/cart.php', [
            'count' => 1,
            'groups' => [
                1 => [
                    'store_name' => 'Fresh Mart',
                    'subtotal' => 12.50,
                    'items' => [
                        [
                            'cart_item_id' => 10,
                            'product_name' => 'Tomato',
                            'added_method' => 'manual',
                            'stock_quantity' => 5,
                            'quantity' => 1,
                            'line_total' => 12.50,
                        ],
                    ],
                ],
            ],
            'subtotal' => 12.50,
            'deliveryFee' => 5.0,
            'total' => 17.50,
        ]);

        $this->assertStringContainsString('Estimated subtotal', $html);
        $this->assertStringContainsString('Delivery cost', $html);
        $this->assertStringContainsString('Total amount', $html);
    }

    public function test_recipe_cart_preview_shows_cost_summary(): void
    {
        $html = $this->render('recipe/cart-preview.php', [
            'recipe' => ['recipe_id' => 7, 'recipe_title' => 'Soup'],
            'servings' => 2,
            'warnings' => [],
            'grouped' => [
                1 => [
                    'store_name' => 'Fresh Mart',
                    'subtotal' => 12.50,
                    'items' => [
                        [
                            'product' => [
                                'product_name' => 'Tomato',
                                'package_quantity' => 500,
                                'package_unit' => 'g',
                            ],
                            'required_packages' => 1,
                            'scaled_quantity' => 250,
                            'unit' => 'g',
                            'ingredient_name' => 'tomato',
                            'line_total' => 12.50,
                        ],
                    ],
                ],
            ],
            'subtotal' => 12.50,
            'deliveryFee' => 5.0,
            'total' => 17.50,
        ]);

        $this->assertStringContainsString('Cost summary', $html);
        $this->assertStringContainsString('Item subtotal', $html);
        $this->assertStringContainsString('Delivery cost', $html);
        $this->assertStringContainsString('Total amount', $html);
    }

    private function render(string $view, array $data): string
    {
        $bufferLevel = ob_get_level();
        set_error_handler(
            static function (int $severity, string $message, string $file, int $line): never {
                throw new ErrorException($message, 0, $severity, $file, $line);
            }
        );

        try {
            extract($data, EXTR_SKIP);
            ob_start();
            require __DIR__ . '/../src/app/views/' . $view;
            return (string) ob_get_clean();
        } finally {
            while (ob_get_level() > $bufferLevel) {
                ob_end_clean();
            }
            restore_error_handler();
        }
    }
}
