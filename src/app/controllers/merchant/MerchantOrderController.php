<?php
declare(strict_types=1);
namespace App\Controllers\Merchant;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Store;
use App\Models\MerchantOrder;
use App\Models\OrderItem;

class MerchantOrderController extends Controller
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

    public function index(): void
    {
        AuthHelper::requireRole('merchant');
        $store = (new Store())->byUser((int) AuthHelper::id());
        if (!$store) { $this->redirect('/merchant/store'); }
        $mo = new MerchantOrder();
        $mo->syncTimedStatusesForStore((int) $store['store_id']);
        $orders = $mo->forStore((int) $store['store_id']);
        $oi = new OrderItem();
        foreach ($orders as &$o) $o['items'] = $oi->forMerchantOrder((int) $o['merchant_order_id']);
        $this->view('merchant/orders', [
            'title' => 'Orders',
            'orders' => $orders,
        ], 'layout/merchant-layout');
    }

    public function updateStatus(string $id): void
    {
        AuthHelper::requireRole('merchant');
        $this->requireCsrf();
        $store = (new Store())->byUser((int) AuthHelper::id());
        $mo = new MerchantOrder();
        $row = $mo->find((int) $id);
        if (!$row || (int) $row['store_id'] !== (int) $store['store_id']) { http_response_code(403); echo 'Forbidden'; return; }
        $status = (string) $this->input('status', 'pending');
        if (!in_array($status, MerchantOrder::STATUSES, true)) {
            Flash::set('error', 'Invalid order status.');
            $this->redirect('/merchant/orders');
        }
        $allowed = [
            'accepted' => ['pending'],
            'cancelled' => ['pending'],
            'preparing' => ['accepted'],
            'ready_to_deliver' => ['preparing'],
        ];
        if (!isset($allowed[$status]) || !in_array((string) $row['status'], $allowed[$status], true)) {
            Flash::set('error', 'This order status cannot be changed backwards or skipped.');
            $this->redirect('/merchant/orders');
        }
        if ($mo->updateStatusAndSyncParent((int) $id, $status)) {
            Flash::set('success', 'Order status updated.');
        } else {
            Flash::set('error', 'Failed to update order status. Please make sure the order tracking migration was applied.');
        }
        $this->redirect('/merchant/orders');
    }

    public function trackingStatus(string $id): void
    {
        AuthHelper::requireRole('merchant');
        $store = (new Store())->byUser((int) AuthHelper::id());
        if (!$store) {
            http_response_code(404);
            echo 'Not found';
            return;
        }

        $mo = new MerchantOrder();
        $row = $mo->belongsToStore((int) $id, (int) $store['store_id']);
        if (!$row) {
            http_response_code(404);
            echo 'Not found';
            return;
        }

        $mo->syncTimedStatusesForOrder((int) $row['order_id']);
        $fresh = $mo->belongsToStore((int) $id, (int) $store['store_id']);

        header('Content-Type: application/json');
        echo json_encode($this->trackingPayload($fresh ?: $row));
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
