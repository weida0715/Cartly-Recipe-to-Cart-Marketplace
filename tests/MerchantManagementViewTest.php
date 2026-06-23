<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}

class MerchantManagementViewTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function test_admin_merchant_page_shows_request_details_and_approval_history(): void
    {
        $html = $this->render('admin/merchant-approval.php', [
            'pending' => [[
                'store_id' => 4,
                'store_name' => 'Fresh Basket',
                'owner_name' => 'Amir Customer',
                'username' => 'amir',
                'account_email' => 'amir@example.test',
                'contact_email' => 'store@example.test',
                'contact_phone' => '0123456789',
                'opening_time' => '08:00:00',
                'closing_time' => '20:00:00',
                'store_address' => '19 Orchard Lane',
                'store_description' => 'Fresh local ingredients.',
                'created_at' => '2026-06-22 10:30:00',
            ]],
            'approvedHistory' => [[
                'store_id' => 2,
                'store_name' => 'Daily Mart',
                'owner_name' => 'Merchant User',
                'username' => 'merchant',
                'contact_email' => 'daily@example.test',
                'created_at' => '2026-06-20 09:00:00',
                'approved_at' => '2026-06-21 11:00:00',
                'store_status' => 'approved',
            ]],
        ]);

        $this->assertStringContainsString('Operating hours', $html);
        $this->assertStringContainsString('19 Orchard Lane', $html);
        $this->assertStringContainsString('Approved merchant request history', $html);
        $this->assertStringContainsString('/admin/merchants/4/approve', $html);
        $this->assertStringContainsString('/admin/merchants/2/close', $html);
    }

    public function test_voucher_page_shows_compact_summary_and_management_actions(): void
    {
        $html = $this->render('merchant/vouchers.php', [
            'vouchers' => [[
                'voucher_id' => 7,
                'voucher_code' => 'FRESH10',
                'discount_type' => 'percentage',
                'discount_value' => '10.00',
                'minimum_spend' => '30.00',
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-30',
                'usage_limit' => 20,
                'used_count' => 3,
                'status' => 'active',
            ]],
        ]);

        $this->assertStringContainsString('voucher-record-summary', $html);
        $this->assertStringContainsString('10% off', $html);
        $this->assertStringContainsString('3 of 20 used', $html);
        $this->assertStringContainsString('Edit', $html);
        $this->assertStringContainsString('/merchant/vouchers/7/delete', $html);
        $this->assertStringContainsString('/merchant/vouchers/7/update', $html);
    }

    public function test_merchant_approval_code_preserves_review_history_and_status_rules(): void
    {
        $controller = file_get_contents(__DIR__ . '/../src/app/controllers/admin/AdminMerchantController.php');
        $storeModel = file_get_contents(__DIR__ . '/../src/app/models/Store.php');
        $migration = file_get_contents(__DIR__ . '/../src/database/migrations/008_merchant_request_reviewed_at.sql');

        $this->assertStringContainsString("'reviewed_at' => date('Y-m-d H:i:s')", $controller);
        $this->assertStringContainsString("store_status'] !== 'pending'", $controller);
        $this->assertStringContainsString("store_status'] !== 'approved'", $controller);
        $this->assertStringContainsString('approvedRequestHistory', $storeModel);
        $this->assertStringContainsString('COALESCE(s.reviewed_at, s.created_at)', $storeModel);
        $this->assertStringContainsString('ADD COLUMN IF NOT EXISTS reviewed_at', $migration);
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
