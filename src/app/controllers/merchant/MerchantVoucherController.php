<?php
declare(strict_types=1);
namespace App\Controllers\Merchant;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Store;
use App\Models\Voucher;

class MerchantVoucherController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireRole('merchant');
        $store = (new Store())->byUser((int) AuthHelper::id());
        if (!$store) {
            $this->redirect('/merchant/store');
        }
        $this->view('merchant/vouchers', [
            'title' => 'Vouchers',
            'vouchers' => (new Voucher())->byStore((int) $store['store_id']),
        ], 'layout/merchant-layout');
    }

    public function store(): void
    {
        AuthHelper::requireRole('merchant');
        $this->requireCsrf();
        $store = (new Store())->byUser((int) AuthHelper::id());
        (new Voucher())->insert([
            'store_id' => (int) $store['store_id'],
            'voucher_code' => strtoupper(trim((string) $this->input('voucher_code'))),
            'discount_type' => (string) $this->input('discount_type', 'fixed'),
            'discount_value' => (float) $this->input('discount_value', 0),
            'minimum_spend' => (float) $this->input('minimum_spend', 0),
            'start_date' => $this->input('start_date') ?: null,
            'end_date' => $this->input('end_date') ?: null,
            'usage_limit' => (int) $this->input('usage_limit', 0),
            'status' => 'active',
        ]);
        Flash::set('success', 'Voucher created.');
        $this->redirect('/merchant/vouchers');
    }

    public function update(string $id): void
    {
        AuthHelper::requireRole('merchant');
        $this->requireCsrf();
        $store = (new Store())->byUser((int) AuthHelper::id());
        $voucher = new Voucher();
        $row = $voucher->find((int) $id);
        if (!$row || !$store || (int) $row['store_id'] !== (int) $store['store_id']) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $voucher->update((int) $id, [
            'voucher_code' => strtoupper(trim((string) $this->input('voucher_code'))),
            'discount_type' => (string) $this->input('discount_type', 'fixed'),
            'discount_value' => (float) $this->input('discount_value', 0),
            'minimum_spend' => (float) $this->input('minimum_spend', 0),
            'start_date' => $this->input('start_date') ?: null,
            'end_date' => $this->input('end_date') ?: null,
            'usage_limit' => (int) $this->input('usage_limit', 0),
            'status' => (string) $this->input('status', 'active'),
        ]);
        Flash::set('success', 'Voucher updated.');
        $this->redirect('/merchant/vouchers');
    }

    public function delete(string $id): void
    {
        AuthHelper::requireRole('merchant');
        $this->requireCsrf();
        $store = (new Store())->byUser((int) AuthHelper::id());
        $v = new Voucher();
        $row = $v->find((int) $id);
        if (!$row || (int) $row['store_id'] !== (int) $store['store_id']) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }
        $v->update((int) $id, ['status' => 'inactive']);
        Flash::set('info', 'Voucher deactivated.');
        $this->redirect('/merchant/vouchers');
    }
}
