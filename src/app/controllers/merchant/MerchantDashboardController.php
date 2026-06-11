<?php
declare(strict_types=1);
namespace App\Controllers\Merchant;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Models\Store;
use App\Models\Product;
use App\Models\MerchantOrder;

class MerchantDashboardController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireRole('merchant');
        $store = (new Store())->byUser((int) AuthHelper::id());
        if (!$store) {
            $this->view('merchant/onboarding', ['title' => 'Merchant', 'store' => null], 'layout/merchant-layout');
            return;
        }
        $products = (new Product())->byStore((int) $store['store_id']);
        $orders = (new MerchantOrder())->forStore((int) $store['store_id']);
        $totals = ['orders' => count($orders), 'revenue' => 0, 'low_stock' => 0, 'products' => count($products)];
        foreach ($orders as $o) $totals['revenue'] += (float) $o['subtotal'] - (float) $o['discount_amount'];
        foreach ($products as $p) if ((int) $p['stock_quantity'] < 5) $totals['low_stock']++;
        $this->view('merchant/dashboard', [
            'title' => 'Merchant Dashboard',
            'store' => $store, 'totals' => $totals, 'orders' => array_slice($orders, 0, 5),
        ], 'layout/merchant-layout');
    }
}
