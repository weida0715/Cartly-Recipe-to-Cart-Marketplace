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
        $totals = ['orders' => count($orders), 'revenue' => 0, 'average_order_value' => 0, 'low_stock' => 0, 'products' => count($products)];
        $weekLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $salesByDay = array_fill_keys($weekLabels, 0);
        $ordersByDay = array_fill_keys($weekLabels, 0);
        $weekStart = strtotime('monday this week');
        $weekEnd = strtotime('monday next week');
        $recentRevenue = 0;
        $previousRevenue = 0;
        foreach ($orders as $o) {
            $revenue = (float) $o['subtotal'] - (float) $o['discount_amount'];
            $totals['revenue'] += $revenue;
            $createdAt = strtotime((string) $o['created_at']);
            if ($createdAt >= $weekStart && $createdAt < $weekEnd) {
                $day = date('D', $createdAt);
                $salesByDay[$day] += $revenue;
                $ordersByDay[$day]++;
            }

            $age = (time() - $createdAt) / 86400;
            if ($age <= 30) $recentRevenue += $revenue;
            elseif ($age <= 60) $previousRevenue += $revenue;
        }
        $totals['average_order_value'] = $totals['orders'] ? $totals['revenue'] / $totals['orders'] : 0;
        $revenueChange = $previousRevenue > 0 ? (($recentRevenue - $previousRevenue) / $previousRevenue) * 100 : ($recentRevenue > 0 ? 100 : 0);
        foreach ($products as $p) if ((int) $p['stock_quantity'] < 5) $totals['low_stock']++;
        $this->view('merchant/dashboard', [
            'title' => 'Merchant Dashboard',
            'store' => $store,
            'totals' => $totals,
            'orders' => array_slice($orders, 0, 5),
            'salesChart' => array_map(fn($day, $value) => ['label' => $day, 'value' => round($value, 2)], array_keys($salesByDay), $salesByDay),
            'orderTrendChart' => array_map(fn($day, $value) => ['label' => $day, 'value' => (int) $value], array_keys($ordersByDay), $ordersByDay),
            'revenueChange' => $revenueChange,
        ], 'layout/merchant-layout');
    }
}
