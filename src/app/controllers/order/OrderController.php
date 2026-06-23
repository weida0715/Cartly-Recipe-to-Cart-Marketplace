<?php
declare(strict_types=1);
namespace App\Controllers\Order;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Order;
use App\Models\MerchantOrder;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;

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
            'title' => 'Order History',
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

    public function receipt(string $id): void
    {
        $data = $this->receiptData((int) $id);
        if ($data === null) {
            return;
        }
        $this->view('order/receipt', ['title' => 'Receipt ' . $data['receiptNumber']] + $data);
    }

    public function downloadReceipt(string $id): void
    {
        $data = $this->receiptData((int) $id);
        if ($data === null) {
            return;
        }
        $safeReceiptNumber = preg_replace('/[^a-zA-Z0-9_-]/', '', $data['receiptNumber']) ?: 'receipt';
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: attachment; filename="cartly-' . strtolower($safeReceiptNumber) . '.html"');
        header('X-Content-Type-Options: nosniff');
        extract($data, EXTR_SKIP);
        $downloadMode = true;
        require dirname(__DIR__, 2) . '/views/order/receipt.php';
    }

    public function received(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $mo = new MerchantOrder();
        $row = $mo->belongsToCustomer((int) $id, (int) AuthHelper::id());
        if (!$row || (string) $row['status'] !== 'delivered') {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }
        if ($mo->updateStatusAndSyncParent((int) $id, 'completed')) {
            Flash::set('success', 'Order marked as received.');
        } else {
            Flash::set('error', 'Failed to mark order as received.');
        }
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
            http_response_code(403);
            echo 'Forbidden';
            return;
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
            http_response_code(404);
            echo 'Order not found';
            return;
        }
        (new MerchantOrder())->syncTimedStatusesForOrder($id);
        $merchantOrders = (new MerchantOrder())->forOrder($id);
        $order['display_order_status'] = (new Order())->displayStatusFromMerchantStatuses(array_column($merchantOrders, 'status'));
        $oi = new OrderItem();
        foreach ($merchantOrders as &$mo) {
            $mo['persisted_tracking_status'] = (string) $mo['status'];
            $mo['items'] = $oi->forMerchantOrder((int) $mo['merchant_order_id']);
        }
        unset($mo);
        $this->view($view, [
            'title' => $title,
            'order' => $order,
            'merchantOrders' => $merchantOrders,
        ]);
    }

    private function receiptData(int $orderId): ?array
    {
        AuthHelper::requireLogin();
        $order = (new Order())->find($orderId);
        if (!$order || (int) $order['user_id'] !== (int) AuthHelper::id()) {
            http_response_code(404);
            echo 'Receipt not found';
            return null;
        }

        $merchantOrders = (new MerchantOrder())->forOrder($orderId);
        $itemModel = new OrderItem();
        foreach ($merchantOrders as &$merchantOrder) {
            $merchantOrder['items'] = $itemModel->forMerchantOrder((int) $merchantOrder['merchant_order_id']);
        }
        unset($merchantOrder);

        $createdAt = strtotime((string) ($order['created_at'] ?? ''));
        $receiptDate = $createdAt === false ? date('Ymd') : date('Ymd', $createdAt);
        return [
            'order' => $order,
            'merchantOrders' => $merchantOrders,
            'payment' => (new PaymentTransaction())->forOrder($orderId),
            'receiptNumber' => (string) (($order['receipt_number'] ?? '')
                ?: 'RCT-' . $receiptDate . '-' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT)),
        ];
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
