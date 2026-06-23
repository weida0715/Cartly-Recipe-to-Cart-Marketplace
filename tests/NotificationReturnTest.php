<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}

class NotificationReturnTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function test_notification_pages_render_unread_and_action_states(): void
    {
        $notification = [
            'notification_id' => 5,
            'type' => 'warning',
            'title' => 'Return request updated',
            'message' => 'The merchant approved your return.',
            'action_url' => '/orders/12',
            'is_read' => 0,
            'created_at' => '2026-06-23 15:00:00',
        ];

        $list = $this->render('notification/index.php', ['notifications' => [$notification]]);
        $detail = $this->render('notification/show.php', ['notification' => $notification]);

        $this->assertStringContainsString('is-unread', $list);
        $this->assertStringContainsString('/notifications/5', $list);
        $this->assertStringContainsString('Open related page', $detail);
        $this->assertStringContainsString('/orders/12', $detail);
    }

    public function test_customer_order_view_shows_cancel_and_after_sale_controls(): void
    {
        $html = $this->render('order/_order-details.php', [
            'order' => ['shipping_address' => 'Test Street', 'contact_phone' => '0123456789'],
            'merchantOrders' => [[
                'merchant_order_id' => 4,
                'store_name' => 'Fresh Basket',
                'status' => 'completed',
                'persisted_tracking_status' => 'completed',
                'subtotal' => 10,
                'discount_amount' => 0,
                'final_amount' => 10,
                'items' => [[
                    'order_item_id' => 8,
                    'product_name_snapshot' => 'Carrots 1kg',
                    'unit_price' => 5,
                    'quantity' => 2,
                    'subtotal' => 10,
                    'return_request' => null,
                ]],
            ]],
        ]);

        $this->assertStringContainsString('Request return/refund', $html);
        $this->assertStringContainsString('/orders/items/8/return-request', $html);
        $this->assertStringContainsString('max="2"', $html);
    }

    public function test_customer_order_view_disables_cancellation_after_dispatch(): void
    {
        $html = $this->render('order/_order-details.php', [
            'order' => ['shipping_address' => 'Test Street', 'contact_phone' => '0123456789'],
            'merchantOrders' => [[
                'merchant_order_id' => 11,
                'store_name' => 'Fresh Basket',
                'status' => 'out_for_delivery',
                'persisted_tracking_status' => 'out_for_delivery',
                'subtotal' => 5,
                'discount_amount' => 0,
                'final_amount' => 5,
                'items' => [],
            ]],
        ]);

        $this->assertStringContainsString('Cancellation is unavailable after dispatch.', $html);
        $this->assertStringContainsString('type="button" disabled', $html);
        $this->assertStringNotContainsString('/orders/merchant/11/cancel', $html);
    }

    public function test_merchant_order_view_shows_return_decision_controls(): void
    {
        $html = $this->render('merchant/orders.php', [
            'orders' => [[
                'merchant_order_id' => 4,
                'username' => 'customer',
                'created_at' => '2026-06-23',
                'status' => 'completed',
                'items' => [],
                'return_requests' => [[
                    'return_request_id' => 9,
                    'product_name_snapshot' => 'Carrots 1kg',
                    'status' => 'pending',
                    'request_type' => 'return',
                    'quantity' => 1,
                    'requested_amount' => 5,
                    'reason' => 'Damaged package',
                ]],
            ]],
        ]);

        $this->assertStringContainsString('Return and refund requests', $html);
        $this->assertStringContainsString('/merchant/returns/9/decide', $html);
        $this->assertStringContainsString('Approve refund', $html);
        $this->assertStringContainsString('Approve return', $html);
    }

    public function test_schema_and_routes_include_notifications_and_returns(): void
    {
        $schema = file_get_contents(__DIR__ . '/../src/database/schema.sql');
        $routes = file_get_contents(__DIR__ . '/../src/routes/web.php');
        $merchantOrder = file_get_contents(__DIR__ . '/../src/app/models/MerchantOrder.php');

        $this->assertStringContainsString('CREATE TABLE notifications', $schema);
        $this->assertStringContainsString('CREATE TABLE return_requests', $schema);
        $this->assertStringContainsString("'partially_refunded','refunded'", $schema);
        $this->assertStringContainsString('/notifications/{id}', $routes);
        $this->assertStringContainsString('/orders/merchant/{id}/cancel', $routes);
        $this->assertStringContainsString('/merchant/returns/{id}/decide', $routes);
        $this->assertStringContainsString('restoreCancelledStock', $merchantOrder);
        $this->assertStringContainsString('FOR UPDATE', $merchantOrder);
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
