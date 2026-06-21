<?php
declare(strict_types=1);
namespace App\Controllers\Order;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Models\Order;
use App\Models\MerchantOrder;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function history(): void
    {
        AuthHelper::requireLogin();
        $this->view('order/history', [
            'title'  => 'Order History',
            'orders' => (new Order())->historyForUser((int) AuthHelper::id()),
        ]);
    }

    public function show(string $id): void
    {
        $this->renderOrder((int) $id, 'order/show', 'Order #' . $id);
    }

    public function confirmation(string $id): void
    {
        $this->renderOrder((int) $id, 'order/confirmation', 'Order Confirmed');
    }

    private function renderOrder(int $id, string $view, string $title): void
    {
        AuthHelper::requireLogin();
        $order = (new Order())->find($id);
        if (!$order || (int) $order['user_id'] !== (int) AuthHelper::id()) {
            http_response_code(404); echo 'Order not found'; return;
        }
        $merchantOrders = (new MerchantOrder())->forOrder($id);
        $order['display_order_status'] = (new Order())->displayStatusFromMerchantStatuses(array_column($merchantOrders, 'status'));
        $oi = new OrderItem();
        foreach ($merchantOrders as &$mo) {
            $mo['items'] = $oi->forMerchantOrder((int) $mo['merchant_order_id']);
        }
        unset($mo);
        $this->view($view, [
            'title' => $title,
            'order' => $order,
            'merchantOrders' => $merchantOrders,
        ]);
    }
}
