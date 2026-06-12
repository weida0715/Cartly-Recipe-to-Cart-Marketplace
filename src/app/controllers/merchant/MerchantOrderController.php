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
    public function index(): void
    {
        AuthHelper::requireRole('merchant');
        $store = (new Store())->byUser((int) AuthHelper::id());
        if (!$store) { $this->redirect('/merchant/store'); }
        $orders = (new MerchantOrder())->forStore((int) $store['store_id']);
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
        $mo->update((int) $id, ['status' => $status]);
        Flash::set('success', 'Order status updated.');
        $this->redirect('/merchant/orders');
    }
}
