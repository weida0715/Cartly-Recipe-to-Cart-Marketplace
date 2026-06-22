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
