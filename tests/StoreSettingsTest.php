<?php
declare(strict_types=1);

use App\Helpers\CartPricing;
use App\Helpers\StoreHours;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', '/uploads');
}

class StoreSettingsTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function test_delivery_fee_is_charged_once_per_merchant_group(): void
    {
        $groups = [1 => ['subtotal' => 20], 4 => ['subtotal' => 15]];

        $this->assertSame(5.0, CartPricing::deliveryFeeForGroups($groups, 2.5));
        $this->assertSame(40.0, CartPricing::totalWithDelivery(35, 5));
        $this->assertSame(17.5, CartPricing::merchantTotal(20, 5, 2.5));
    }

    public function test_delivery_fee_never_becomes_negative(): void
    {
        $this->assertSame(0.0, CartPricing::deliveryFeeForGroups([1 => []], -4));
        $this->assertSame(0.0, CartPricing::merchantTotal(5, 10, -2));
    }

    public function test_store_hours_require_valid_distinct_times(): void
    {
        $this->assertNull(StoreHours::error('08:00', '20:00'));
        $this->assertNull(StoreHours::error('20:00', '06:00'));
        $this->assertSame('08:00:00', StoreHours::normalize('08:00'));
        $this->assertSame('Opening time and closing time are required.', StoreHours::error('', '20:00'));
        $this->assertSame('Enter valid store operating hours.', StoreHours::error('25:00', '20:00'));
        $this->assertSame('Opening time and closing time must be different.', StoreHours::error('08:00', '08:00'));
    }

    public function test_admin_settings_view_renders_delivery_fee_control(): void
    {
        $html = $this->render('admin/settings.php', ['deliveryFee' => 3.5]);

        $this->assertStringContainsString('name="delivery_fee"', $html);
        $this->assertStringContainsString('value="3.50"', $html);
        $this->assertStringContainsString('per merchant', $html);
    }

    public function test_store_profile_renders_statistics_and_operating_hours_cards(): void
    {
        $html = $this->render('merchant/store.php', [
            'store' => [
                'store_id' => 1,
                'store_name' => 'Green Store',
                'store_status' => 'approved',
                'opening_time' => '08:00:00',
                'closing_time' => '20:00:00',
                'rating' => 4.5,
            ],
            'statistics' => [
                'active_products' => 7,
                'total_orders' => 12,
                'revenue' => 140.5,
            ],
        ]);

        $this->assertStringContainsString('Store statistics', $html);
        $this->assertStringContainsString('Operating hours', $html);
        $this->assertStringContainsString('8:00 AM - 8:00 PM', $html);
        $this->assertStringContainsString('RM 140.50', $html);
    }

    public function test_checkout_uses_saved_delivery_fee_setting(): void
    {
        $checkout = file_get_contents(__DIR__ . '/../src/app/controllers/order/CheckoutController.php');
        $this->assertIsString($checkout);
        $this->assertStringContainsString('(new AppSetting())->deliveryFee()', $checkout);
        $this->assertStringContainsString("':df' => \$deliveryFee", $checkout);
        $this->assertStringContainsString('CartPricing::merchantTotal', $checkout);
    }

    public function test_schema_and_order_details_include_delivery_fee_support(): void
    {
        $schema = file_get_contents(__DIR__ . '/../src/database/schema.sql');
        $orderDetails = file_get_contents(__DIR__ . '/../src/app/views/order/_order-details.php');

        $this->assertIsString($schema);
        $this->assertIsString($orderDetails);
        $this->assertStringContainsString("CREATE TABLE application_settings (", $schema);
        $this->assertStringContainsString(");\nCREATE TABLE users (", str_replace("\r\n", "\n", $schema));
        $this->assertStringContainsString("'delivery_fee'", $orderDetails);
    }

    public function test_application_settings_migration_is_idempotent(): void
    {
        $migration = file_get_contents(__DIR__ . '/../src/database/migrations/007_application_settings.sql');

        $this->assertIsString($migration);
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS application_settings', $migration);
        $this->assertStringContainsString('INSERT IGNORE INTO application_settings', $migration);
    }

    private function render(string $view, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require __DIR__ . '/../src/app/views/' . $view;
        return (string) ob_get_clean();
    }
}