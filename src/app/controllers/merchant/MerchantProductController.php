<?php
declare(strict_types=1);
namespace App\Controllers\Merchant;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Helpers\FileUploadHelper;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Ingredient;

class MerchantProductController extends Controller
{
    private function currentStore(): array
    {
        AuthHelper::requireRole('merchant');
        $s = (new Store())->byUser((int) AuthHelper::id());
        if (!$s) {
            Flash::set('error', 'Create a store first.');
            $this->redirect('/merchant/store');
        }
        return $s;
    }

    public function index(): void
    {
        $store = $this->currentStore();
        $this->view('merchant/products', [
            'title' => 'Products',
            'products' => (new Product())->byStore((int) $store['store_id']),
        ], 'layout/merchant-layout');
    }

    public function create(): void
    {
        $this->currentStore();
        $this->view('merchant/product-form', [
            'title' => 'New Product',
            'product' => null,
            'cats' => (new Category())->active(),
            'ingredients' => (new Ingredient())->all('ingredient_name'),
        ], 'layout/merchant-layout');
    }

    /** POST /merchant/products */
    public function store(): void
    {
        $store = $this->currentStore();
        $this->requireCsrf();
        try {
            (new Product())->insert($this->payload((int) $store['store_id']));
        } catch (\RuntimeException $e) {
            Flash::set('error', $e->getMessage());
            $this->redirect('/merchant/products/create');
        }
        Flash::set('success', 'Product created.');
        $this->redirect('/merchant/products');
    }

    public function edit(string $id): void
    {
        $store = $this->currentStore();
        $p = (new Product())->find((int) $id);
        if (!$p || (int) $p['store_id'] !== (int) $store['store_id']) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }
        $this->view('merchant/product-form', [
            'title' => 'Edit Product',
            'product' => $p,
            'cats' => (new Category())->active(),
            'ingredients' => (new Ingredient())->all('ingredient_name'),
        ], 'layout/merchant-layout');
    }

    public function update(string $id): void
    {
        $store = $this->currentStore();
        $this->requireCsrf();
        $pm = new Product();
        $p = $pm->find((int) $id);
        if (!$p || (int) $p['store_id'] !== (int) $store['store_id']) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }
        try {
            $data = $this->payload((int) $store['store_id'], false);
            $newImage = $data['image'] ?? null;
            $pm->update((int) $id, $data);
            if ($newImage && !empty($p['image']) && $p['image'] !== $newImage) {
                FileUploadHelper::delete($p['image']);
            }
        } catch (\RuntimeException $e) {
            Flash::set('error', $e->getMessage());
            $this->redirect('/merchant/products/' . $id . '/edit');
        }
        Flash::set('success', 'Product updated.');
        $this->redirect('/merchant/products');
    }

    public function delete(string $id): void
    {
        $store = $this->currentStore();
        $this->requireCsrf();
        $pm = new Product();
        $p = $pm->find((int) $id);
        if (!$p || (int) $p['store_id'] !== (int) $store['store_id']) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }
        $pm->update((int) $id, ['status' => 'inactive']);
        Flash::set('info', 'Product deactivated.');
        $this->redirect('/merchant/products');
    }

    private function payload(int $storeId, bool $forInsert = true): array
    {
        $data = [
            'store_id' => $storeId,
            'category_id' => (int) $this->input('category_id') ?: null,
            'ingredient_id' => (int) $this->input('ingredient_id') ?: null,
            'product_name' => trim((string) $this->input('product_name', '')),
            'description' => (string) $this->input('description', ''),
            'price' => (float) $this->input('price', 0),
            'stock_quantity' => (int) $this->input('stock_quantity', 0),
            'package_quantity' => (float) $this->input('package_quantity', 1),
            'package_unit' => (string) $this->input('package_unit', ''),
            'status' => (string) $this->input('status', 'active'),
        ];
        if (!$forInsert)
            unset($data['store_id']);
        $image = FileUploadHelper::image('image', 'products');
        if ($image !== null) {
            $data['image'] = $image;
        }
        return $data;
    }
}
