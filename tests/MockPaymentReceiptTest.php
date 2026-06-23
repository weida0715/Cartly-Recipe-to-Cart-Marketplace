<?php
declare(strict_types=1);

use App\Helpers\MockPaymentGateway;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}

class MockPaymentReceiptTest extends TestCase
{
    public function test_valid_card_is_approved_without_returning_sensitive_fields(): void
    {
        $result = (new MockPaymentGateway())->process('card', [
            'cardholder_name' => 'Test Customer',
            'card_number' => '4242 4242 4242 4242',
            'card_expiry' => date('m/y', strtotime('+2 years')),
            'card_cvv' => '123',
        ], 42.50);

        $this->assertSame('approved', $result['status']);
        $this->assertSame('Card ending 4242', $result['masked_account']);
        $this->assertSame(42.50, $result['amount']);
        $this->assertArrayNotHasKey('card_number', $result);
        $this->assertArrayNotHasKey('card_cvv', $result);
    }

    public function test_invalid_or_declined_card_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);
        (new MockPaymentGateway())->process('card', [
            'cardholder_name' => 'Test Customer',
            'card_number' => '1234',
            'card_expiry' => '12/30',
            'card_cvv' => '123',
        ], 10.00);
    }

    public function test_array_manipulated_payment_fields_are_rejected_without_warnings(): void
    {
        $this->expectException(RuntimeException::class);
        (new MockPaymentGateway())->process('card', [
            'cardholder_name' => ['Test Customer'],
            'card_number' => ['4242424242424242'],
            'card_expiry' => ['12/30'],
            'card_cvv' => ['123'],
        ], 10.00);
    }

    public function test_decline_test_card_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('declined');
        (new MockPaymentGateway())->process('card', [
            'cardholder_name' => 'Test Customer',
            'card_number' => '4000 0000 0000 0002',
            'card_expiry' => date('m/y', strtotime('+2 years')),
            'card_cvv' => '123',
        ], 10.00);
    }

    public function test_online_banking_and_ewallet_are_approved_with_masked_details(): void
    {
        $gateway = new MockPaymentGateway();
        $bank = $gateway->process('online_banking', [
            'bank_account_name' => 'Test Customer',
            'bank_name' => 'maybank',
        ], 15.00);
        $wallet = $gateway->process('ewallet', [
            'ewallet_name' => 'Test Customer',
            'ewallet_provider' => 'touch_n_go',
            'ewallet_phone' => '0123456789',
        ], 15.00);

        $this->assertSame('Maybank Mock FPX', $bank['provider_name']);
        $this->assertSame('FPX authenticated account', $bank['masked_account']);
        $this->assertSame('Touch N Go', $wallet['provider_name']);
        $this->assertSame('Wallet phone ending 6789', $wallet['masked_account']);
    }

    public function test_zero_total_order_requires_no_charge(): void
    {
        $result = (new MockPaymentGateway())->process('card', [], 0.0);

        $this->assertSame('approved', $result['status']);
        $this->assertSame('No charge required', $result['masked_account']);
        $this->assertSame(0.0, $result['amount']);
    }

    public function test_receipt_renders_snapshots_items_and_masked_transaction(): void
    {
        $html = $this->renderReceipt([
            'order' => [
                'order_id' => 12,
                'customer_name_snapshot' => 'Test Customer',
                'customer_email_snapshot' => 'test@example.test',
                'shipping_address' => '12 Market Street',
                'created_at' => '2026-06-23 15:00:00',
                'payment_status' => 'paid',
                'payment_method' => 'card',
                'total_amount' => 15.00,
            ],
            'merchantOrders' => [[
                'store_name' => 'Fresh Basket',
                'subtotal' => 20.00,
                'discount_amount' => 5.00,
                'delivery_fee' => 0.00,
                'items' => [[
                    'product_name_snapshot' => 'Carrots 1kg',
                    'unit_price' => 10.00,
                    'quantity' => 2,
                    'subtotal' => 20.00,
                ]],
            ]],
            'payment' => [
                'transaction_reference' => 'PAY-TEST-123',
                'provider_name' => 'Mock Visa/Mastercard',
                'masked_account' => 'Card ending 4242',
            ],
            'receiptNumber' => 'RCT-20260623-000012',
        ]);

        $this->assertStringContainsString('RCT-20260623-000012', $html);
        $this->assertStringContainsString('PAY-TEST-123', $html);
        $this->assertStringContainsString('Card ending 4242', $html);
        $this->assertStringContainsString('Carrots 1kg', $html);
        $this->assertStringContainsString('RM 15.00', $html);
    }

    public function test_schema_routes_and_checkout_use_payment_transaction_flow(): void
    {
        $schema = file_get_contents(__DIR__ . '/../src/database/schema.sql');
        $routes = file_get_contents(__DIR__ . '/../src/routes/web.php');
        $checkout = file_get_contents(__DIR__ . '/../src/app/controllers/order/CheckoutController.php');
        $orderController = file_get_contents(__DIR__ . '/../src/app/controllers/order/OrderController.php');
        $receipt = file_get_contents(__DIR__ . '/../src/app/views/order/receipt.php');
        $migration = file_get_contents(__DIR__ . '/../src/database/migrations/010_mock_payments_and_receipts.sql');

        $this->assertStringContainsString('CREATE TABLE payment_transactions', $schema);
        $this->assertStringContainsString('receipt_number', $schema);
        $this->assertStringContainsString('uniq_orders_receipt_number', file_get_contents(__DIR__ . '/../src/database/migrations/010_mock_payments_and_receipts.sql'));
        $this->assertStringContainsString('/orders/{id}/receipt/download', $routes);
        $this->assertStringContainsString("payment_status = 'paid'", $checkout);
        $this->assertStringContainsString('MockPaymentGateway', $checkout);
        $this->assertStringNotContainsString("'card_number' =>", $checkout);
        $this->assertStringNotContainsString("'card_cvv' =>", $checkout);
        $this->assertStringContainsString("require dirname(__DIR__, 2) . '/views/order/receipt.php';\n        exit;", $orderController);
        $this->assertStringContainsString('is_array($merchantOrders ?? null)', $receipt);
        $this->assertStringContainsString("COALESCE(u.full_name, '')", $migration);
        $this->assertStringContainsString("COALESCE(u.email, '')", $migration);
    }

    private function renderReceipt(array $data): string
    {
        extract($data, EXTR_SKIP);
        $downloadMode = false;
        ob_start();
        require __DIR__ . '/../src/app/views/order/receipt.php';
        return (string) ob_get_clean();
    }
}
