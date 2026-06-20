<?php
declare(strict_types=1);
namespace App\Controllers\Store;

use App\Helpers\Controller;
use App\Models\Product;
use App\Models\Store;
use App\Models\Voucher;

class StoreController extends Controller
{
    public function index(): void
    {
        $q = trim((string) $this->input('q', ''));
        $stores = (new Store())->approved();
        if ($q !== '') {
            $needle = mb_strtolower($q);
            $stores = array_values(array_filter($stores, static function (array $store) use ($needle): bool {
                $haystack = mb_strtolower(
                    (string) ($store['store_name'] ?? '') . ' ' .
                    (string) ($store['store_description'] ?? '') . ' ' .
                    (string) ($store['store_address'] ?? '')
                );
                return str_contains($haystack, $needle);
            }));
        }

        $productModel = new Product();
        $voucherModel = new Voucher();
        foreach ($stores as &$store) {
            $storeId = (int) $store['store_id'];
            $products = $productModel->activeByStore($storeId);
            $store['product_count'] = count($products);
            $store['voucher_count'] = count($voucherModel->activeByStore($storeId));
            $store['featured_products'] = array_slice($products, 0, 3);
        }
        unset($store);

        $this->view('store/index', [
            'title' => 'Stores · Cartly',
            'stores' => $stores,
            'q' => $q,
        ]);
    }

    public function show(string $id): void
    {
        $store = (new Store())->findApproved((int) $id);
        if (!$store) {
            http_response_code(404);
            echo 'Store not found';
            return;
        }

        $storeId = (int) $store['store_id'];
        $productModel = new Product();
        $voucherModel = new Voucher();

        $store['products'] = $productModel->activeByStore($storeId);
        $store['vouchers'] = $voucherModel->activeByStore($storeId);

        $this->view('store/show', [
            'title' => $store['store_name'] . ' · Cartly',
            'store' => $store,
        ]);
    }
}
