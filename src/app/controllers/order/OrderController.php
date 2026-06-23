<?php
declare(strict_types=1);
namespace App\Controllers\Order;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Order;
use App\Models\MerchantOrder;
use App\Models\OrderItem;
use App\Models\Notification;
use App\Models\ReturnRequest;

class OrderController extends Controller
{
    private const TRACKING_LABELS = [
        'pending' => 'Order placed',
        'accepted' => 'Merchant accepted',
        'preparing' => 'Merchant preparing',
        'ready_to_deliver' => 'Ready to deliver',
        'out_for_delivery' => 'Out for delivery',
        'delivered' => 'Delivered',
        'completed' => 'Order complete',
        'cancelled' => 'Merchant cancelled',
    ];

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

    public function received(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $mo = new MerchantOrder();
        $row = $mo->belongsToCustomer((int) $id, (int) AuthHelper::id());
        if (!$row || (string) $row['status'] !== 'delivered') {
            http_response_code(403); echo 'Forbidden'; return;
        }
        if ($mo->updateStatusAndSyncParent((int) $id, 'completed')) {
            Flash::set('success', 'Order marked as received.');
        } else {
            Flash::set('error', 'Failed to mark order as received.');
        }
        $this->redirect('/orders/' . (int) $row['order_id']);
    }

    public function cancel(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $model = new MerchantOrder();
        $row = $model->belongsToCustomer((int) $id, (int) AuthHelper::id());
        $cancellable = ['pending', 'accepted', 'preparing', 'ready_to_deliver'];
        if (!$row || !in_array((string) $row['status'], $cancellable, true)) {
            Flash::set('error', 'This order can no longer be cancelled.');
            $this->redirect('/orders');
        }
        if (!$model->updateStatusIfCurrentAndSyncParent((int) $id, $cancellable, 'cancelled')) {
            Flash::set('error', 'The order status changed before cancellation could be completed.');
            $this->redirect('/orders/' . (int) $row['order_id']);
        }
        (new Notification())->createForStore(
            (int) $row['store_id'],
            'warning',
            'Customer cancelled an order',
            'Store order #' . (int) $id . ' was cancelled before dispatch.',
            '/merchant/orders'
        );
        Flash::set('success', 'Order cancelled and stock restored.');
        $this->redirect('/orders/' . (int) $row['order_id']);
    }

    public function advanceDelivery(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $target = (string) $this->input('status', '');
        $allowed = [
            'out_for_delivery' => ['ready_to_deliver'],
            'delivered' => ['out_for_delivery'],
        ];
        $mo = new MerchantOrder();
        $row = $mo->belongsToCustomer((int) $id, (int) AuthHelper::id());
        if (!$row || !isset($allowed[$target]) || !in_array((string) $row['status'], $allowed[$target], true)) {
            http_response_code(403); echo 'Forbidden'; return;
        }
        if (!$mo->updateStatusAndSyncParent((int) $id, $target)) {
            if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Failed to update delivery status']);
                return;
            }
            Flash::set('error', 'Failed to update delivery status.');
            $this->redirect('/orders/' . (int) $row['order_id']);
        }
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
            header('Content-Type: application/json');
            $fresh = $mo->belongsToCustomer((int) $id, (int) AuthHelper::id());
            echo json_encode($this->trackingPayload($fresh ?: ['status' => $target]));
            return;
        }
        $this->redirect('/orders/' . (int) $row['order_id']);
    }

    public function trackingStatus(string $id): void
    {
        AuthHelper::requireLogin();
        $mo = new MerchantOrder();
        $row = $mo->belongsToCustomer((int) $id, (int) AuthHelper::id());
        if (!$row) {
            http_response_code(404);
            echo 'Not found';
            return;
        }

        $mo->syncTimedStatusesForOrder((int) $row['order_id']);
        $fresh = $mo->belongsToCustomer((int) $id, (int) AuthHelper::id());

        header('Content-Type: application/json');
        echo json_encode($this->trackingPayload($fresh ?: $row));
    }

    private function renderOrder(int $id, string $view, string $title): void
    {
        AuthHelper::requireLogin();
        $order = (new Order())->find($id);
        if (!$order || (int) $order['user_id'] !== (int) AuthHelper::id()) {
            http_response_code(404); echo 'Order not found'; return;
        }
        (new MerchantOrder())->syncTimedStatusesForOrder($id);
        $merchantOrders = (new MerchantOrder())->forOrder($id);
        $order['display_order_status'] = (new Order())->displayStatusFromMerchantStatuses(array_column($merchantOrders, 'status'));
        $oi = new OrderItem();
        $returnRequestModel = new ReturnRequest();
        foreach ($merchantOrders as &$mo) {
            $mo['persisted_tracking_status'] = (string) $mo['status'];
            $mo['items'] = $oi->forMerchantOrder((int) $mo['merchant_order_id']);
            $requests = $returnRequestModel->forMerchantOrder((int) $mo['merchant_order_id']);
            $requestsByItem = [];
            foreach ($requests as $request) {
                $requestsByItem[(int) $request['order_item_id']] = $request;
            }
            foreach ($mo['items'] as &$item) {
                $item['return_request'] = $requestsByItem[(int) $item['order_item_id']] ?? null;
            }
            unset($item);
        }
        unset($mo);
        $this->view($view, [
            'title' => $title,
            'order' => $order,
            'merchantOrders' => $merchantOrders,
        ]);
    }

    private function trackingPayload(array $row): array
    {
        $status = (string) ($row['status'] ?? 'pending');
        $step = match ($status) {
            'pending' => 1,
            'accepted' => 2,
            'preparing' => 3,
            'ready_to_deliver' => 4,
            'out_for_delivery' => 5,
            'delivered' => 6,
            'completed' => 7,
            'cancelled' => 2,
            default => 1,
        };

        return [
            'status' => $status,
            'step' => $step,
            'label' => self::TRACKING_LABELS[$status] ?? self::TRACKING_LABELS['pending'],
            'badge' => str_replace('_', ' ', $status),
            'showReceived' => $status === 'delivered',
        ];
    }
}
