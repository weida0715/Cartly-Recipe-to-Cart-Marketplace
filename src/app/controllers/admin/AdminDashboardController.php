<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Models\Category;

class AdminDashboardController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireRole('admin');
        $db = db();
        $changeFromPreviousPeriod = static function (float $recent, float $previous): float {
            if ($previous > 0) {
                return (($recent - $previous) / $previous) * 100;
            }

            return $recent > 0 ? 100.0 : 0.0;
        };

        $currentStats = [
            'users' => (int) $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'active_merchants' => (int) $db->query("SELECT COUNT(*) FROM stores WHERE store_status='approved'")->fetchColumn(),
            'revenue' => (float) $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status='paid' AND order_status != 'cancelled'")->fetchColumn(),
            'orders' => (int) $db->query("SELECT COUNT(*) FROM orders WHERE order_status != 'cancelled'")->fetchColumn(),
        ];

        $stats = [
            'users' => $currentStats['users'],
            'active_merchants' => $currentStats['active_merchants'],
            'revenue' => $currentStats['revenue'],
            'orders' => $currentStats['orders'],
            'customers' => (int) $db->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),
            'merchant_accounts' => (int) $db->query("SELECT COUNT(*) FROM users WHERE role='merchant'")->fetchColumn(),
            'pending' => (int) $db->query("SELECT COUNT(*) FROM stores WHERE store_status='pending'")->fetchColumn(),
            'products' => (int) $db->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn(),
            'reports' => (int) $db->query("SELECT COUNT(*) FROM reports WHERE status='pending'")->fetchColumn(),
        ];

        $recentStart = date('Y-m-d H:i:s', strtotime('-30 days'));
        $previousStart = date('Y-m-d H:i:s', strtotime('-60 days'));
        $currentStart = $recentStart;

        $periodCount = static function (string $sql, array $params) use ($db): float {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return (float) $stmt->fetchColumn();
        };

        $changes = [
            'users' => $changeFromPreviousPeriod(
                $periodCount("SELECT COUNT(*) FROM users WHERE created_at >= :recentStart", [':recentStart' => $recentStart]),
                $periodCount("SELECT COUNT(*) FROM users WHERE created_at >= :previousStart AND created_at < :currentStart", [':previousStart' => $previousStart, ':currentStart' => $currentStart])
            ),
            'active_merchants' => $changeFromPreviousPeriod(
                $periodCount("SELECT COUNT(*) FROM stores WHERE store_status='approved' AND created_at >= :recentStart", [':recentStart' => $recentStart]),
                $periodCount("SELECT COUNT(*) FROM stores WHERE store_status='approved' AND created_at >= :previousStart AND created_at < :currentStart", [':previousStart' => $previousStart, ':currentStart' => $currentStart])
            ),
            'revenue' => $changeFromPreviousPeriod(
                $periodCount("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status='paid' AND order_status != 'cancelled' AND created_at >= :recentStart", [':recentStart' => $recentStart]),
                $periodCount("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status='paid' AND order_status != 'cancelled' AND created_at >= :previousStart AND created_at < :currentStart", [':previousStart' => $previousStart, ':currentStart' => $currentStart])
            ),
            'orders' => $changeFromPreviousPeriod(
                $periodCount("SELECT COUNT(*) FROM orders WHERE order_status != 'cancelled' AND created_at >= :recentStart", [':recentStart' => $recentStart]),
                $periodCount("SELECT COUNT(*) FROM orders WHERE order_status != 'cancelled' AND created_at >= :previousStart AND created_at < :currentStart", [':previousStart' => $previousStart, ':currentStart' => $currentStart])
            ),
        ];

        $monthStart = (new \DateTimeImmutable('first day of this month 00:00:00'))->modify('-5 months');
        $monthCursor = $monthStart;
        $monthKeys = [];
        $monthLabels = [];
        for ($i = 0; $i < 6; $i++) {
            $monthKeys[] = $monthCursor->format('Y-m');
            $monthLabels[$monthCursor->format('Y-m')] = $monthCursor->format('M');
            $monthCursor = $monthCursor->modify('+1 month');
        }
        $monthStartSql = $monthStart->format('Y-m-d H:i:s');

        $mapMonthlyTotals = static function (string $sql, array $params) use ($db): array {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $rows = [];
            foreach ($stmt->fetchAll() as $row) {
                $rows[(string) $row['bucket']] = (float) $row['total'];
            }
            return $rows;
        };

        $userBase = (int) $periodCount("SELECT COUNT(*) FROM users WHERE created_at < :start", [':start' => $monthStartSql]);
        $merchantBase = (int) $periodCount("SELECT COUNT(*) FROM stores WHERE store_status='approved' AND created_at < :start", [':start' => $monthStartSql]);
        $userGrowthRows = $mapMonthlyTotals(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS bucket, COUNT(*) AS total
             FROM users
             WHERE created_at >= :start
             GROUP BY bucket
             ORDER BY bucket",
            [':start' => $monthStartSql]
        );
        $merchantGrowthRows = $mapMonthlyTotals(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS bucket, COUNT(*) AS total
             FROM stores
             WHERE store_status='approved' AND created_at >= :start
             GROUP BY bucket
             ORDER BY bucket",
            [':start' => $monthStartSql]
        );

        $userRunning = $userBase;
        $merchantRunning = $merchantBase;
        $userGrowth = [];
        $merchantGrowth = [];
        foreach ($monthKeys as $monthKey) {
            $userRunning += (int) ($userGrowthRows[$monthKey] ?? 0);
            $merchantRunning += (int) ($merchantGrowthRows[$monthKey] ?? 0);
            $userGrowth[] = ['label' => $monthLabels[$monthKey], 'value' => $userRunning];
            $merchantGrowth[] = ['label' => $monthLabels[$monthKey], 'value' => $merchantRunning];
        }

        $revenueByMonth = $mapMonthlyTotals(
            "SELECT DATE_FORMAT(o.created_at, '%Y-%m') AS bucket,
                    COALESCE(SUM(mo.subtotal - COALESCE(mo.discount_amount, 0)), 0) AS total
             FROM merchant_orders mo
             JOIN orders o ON o.order_id = mo.order_id
             WHERE o.payment_status='paid'
               AND mo.status != 'cancelled'
               AND o.created_at >= :start
             GROUP BY bucket
             ORDER BY bucket",
            [':start' => $monthStartSql]
        );
        $revenueChart = [];
        foreach ($monthKeys as $monthKey) {
            $revenueChart[] = ['label' => $monthLabels[$monthKey], 'value' => round((float) ($revenueByMonth[$monthKey] ?? 0), 2)];
        }

        $activeCategories = (new Category())->active();
        $categoryCountsStmt = $db->query(
            "SELECT c.category_id, c.category_name, COUNT(p.product_id) AS total
             FROM categories c
             LEFT JOIN products p
               ON p.category_id = c.category_id
              AND p.status = 'active'
             WHERE c.status = 'active'
             GROUP BY c.category_id, c.category_name
             ORDER BY c.category_name"
        );
        $categoryCounts = [];
        foreach ($categoryCountsStmt->fetchAll() as $row) {
            $categoryCounts[(string) $row['category_id']] = [
                'label' => (string) $row['category_name'],
                'value' => (int) $row['total'],
            ];
        }

        $categoryChart = array_map(
            static fn(array $category): array => $categoryCounts[(string) $category['category_id']] ?? [
                'label' => (string) $category['category_name'],
                'value' => 0,
            ],
            $activeCategories
        );

        $uncategorizedCount = (int) $db->query("SELECT COUNT(*) FROM products WHERE category_id IS NULL AND status = 'active'")->fetchColumn();
        if ($uncategorizedCount > 0) {
            $categoryChart[] = ['label' => 'Uncategorized', 'value' => $uncategorizedCount];
        }

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'changes' => $changes,
            'growthChartSeries' => [
                ['label' => 'Users', 'values' => $userGrowth],
                ['label' => 'Active merchants', 'values' => $merchantGrowth],
            ],
            'revenueChart' => $revenueChart,
            'categoryChart' => $categoryChart,
            'loadD3' => true,
        ], 'layout/admin-layout');
    }
}
