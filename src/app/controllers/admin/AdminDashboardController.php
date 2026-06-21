<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;

class AdminDashboardController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireRole('admin');
        $db = db();
        $stats = [
            'users'    => (int) $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'customers'=> (int) $db->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),
            'merchants'=> (int) $db->query("SELECT COUNT(*) FROM users WHERE role='merchant'")->fetchColumn(),
            'pending'  => (int) $db->query("SELECT COUNT(*) FROM stores WHERE store_status='pending'")->fetchColumn(),
            'stores'   => (int) $db->query("SELECT COUNT(*) FROM stores WHERE store_status='approved'")->fetchColumn(),
            'products' => (int) $db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
            'orders'   => (int) $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'reports'  => (int) $db->query("SELECT COUNT(*) FROM reports WHERE status='pending'")->fetchColumn(),
        ];
        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'loadD3' => true,
        ], 'layout/admin-layout');
    }
}
