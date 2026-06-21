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
        if (!$store) {
            Flash::set('error', 'Please create a store before adding vouchers.');
            $this->redirect('/merchant/store');
        }

        $code = strtoupper(trim((string) $this->input('voucher_code', '')));
        $discountType = (string) $this->input('discount_type', 'fixed');
        $discountValue = (float) $this->input('discount_value', 0);
        $minimumSpend = (float) $this->input('minimum_spend', 0);
        $usageLimit = (int) $this->input('usage_limit', 0);
        $noExpiry = $this->input('no_expiry') !== null;
        $startDate = $this->input('start_date') ?: null;
        $endDate = $this->input('end_date') ?: null;

        if ($code === '') {
            Flash::set('error', 'Voucher code is required.');
            $this->redirect('/merchant/vouchers');
        }

        if (!in_array($discountType, ['fixed', 'percentage'], true)) {
            Flash::set('error', 'Discount type is invalid.');
            $this->redirect('/merchant/vouchers');
        }

        if ($discountValue <= 0) {
            Flash::set('error', 'Discount value must be greater than zero.');
            $this->redirect('/merchant/vouchers');
        }

        if ($discountType === 'percentage' && $discountValue > 100) {
            Flash::set('error', 'Percentage discount cannot exceed 100%.');
            $this->redirect('/merchant/vouchers');
        }

        if ($minimumSpend < 0) {
            Flash::set('error', 'Minimum spend cannot be negative.');
            $this->redirect('/merchant/vouchers');
        }

        if ($usageLimit < 0) {
            Flash::set('error', 'Usage limit cannot be negative.');
            $this->redirect('/merchant/vouchers');
        }

        if ($noExpiry) {
            $startDate = null;
            $endDate = null;
        } else {
            if ($startDate === null || $endDate === null) {
                Flash::set('error', 'Start date and end date are required unless no expiry date is checked.');
                $this->redirect('/merchant/vouchers');
            }
            try {
                $startDateTime = $startDate !== null ? new \DateTimeImmutable((string) $startDate) : null;
                $endDateTime = $endDate !== null ? new \DateTimeImmutable((string) $endDate) : null;
            } catch (\Exception $e) {
                Flash::set('error', 'Voucher dates are invalid.');
                $this->redirect('/merchant/vouchers');
            }

            if ($startDateTime !== null && $endDateTime !== null && $startDateTime > $endDateTime) {
                Flash::set('error', 'End date must be after or equal to start date.');
                $this->redirect('/merchant/vouchers');
            }
        }

        $voucher = new Voucher();
        try {
            $voucher->insert([
                'store_id' => (int) $store['store_id'],
                'voucher_code' => $code,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'minimum_spend' => $minimumSpend,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'usage_limit' => $usageLimit,
                'status' => 'active',
            ]);
        } catch (\PDOException $e) {
            if ((string) $e->getCode() === '23000') {
                Flash::set('error', 'Voucher code already exists for this store.');
                $this->redirect('/merchant/vouchers');
            }
            throw $e;
        }
        Flash::set('success', 'Voucher created.');
        $this->redirect('/merchant/vouchers');
    }

    public function update(string $id): void
    {
        AuthHelper::requireRole('merchant');
        $this->requireCsrf();
        $store = (new Store())->byUser((int) AuthHelper::id());
        if (!$store) {
            Flash::set('error', 'Store not found.');
            $this->redirect('/merchant/vouchers');
            return;
        }

        $voucherModel = new Voucher();
        $row = $voucherModel->find((int) $id);
        if (!$row || (int) $row['store_id'] !== (int) $store['store_id']) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $code = strtoupper(trim((string) $this->input('voucher_code', '')));
        $discountType = (string) $this->input('discount_type', 'fixed');
        $discountValue = (float) $this->input('discount_value', 0);
        $minSpend = (float) $this->input('minimum_spend', 0);
        $usageLimit = (int) $this->input('usage_limit', 0);
        $noExpiry = $this->input('no_expiry') !== null;
        $startDate = $this->input('start_date') ?: null;
        $endDate = $this->input('end_date') ?: null;

        if ($code === '') {
            Flash::set('error', 'Voucher code is required.');
            $this->redirect('/merchant/vouchers');
            return;
        }

        if (!in_array($discountType, ['fixed', 'percentage'], true)) {
            Flash::set('error', 'Invalid discount type.');
            $this->redirect('/merchant/vouchers');
            return;
        }

        if ($discountValue <= 0) {
            Flash::set('error', 'Discount value must be greater than zero.');
            $this->redirect('/merchant/vouchers');
            return;
        }

        if ($discountType === 'percentage' && $discountValue > 100) {
            Flash::set('error', 'Percentage discount cannot exceed 100%.');
            $this->redirect('/merchant/vouchers');
            return;
        }

        if ($minSpend < 0) {
            Flash::set('error', 'Minimum spend cannot be negative.');
            $this->redirect('/merchant/vouchers');
            return;
        }

        if ($usageLimit < 0) {
            Flash::set('error', 'Usage limit cannot be negative.');
            $this->redirect('/merchant/vouchers');
            return;
        }

        if ($noExpiry) {
            $startDate = null;
            $endDate = null;
        } else {
            if ($startDate === null || $endDate === null) {
                Flash::set('error', 'Start date and end date are required unless no expiry date is checked.');
                $this->redirect('/merchant/vouchers');
                return;
            }
            try {
                $startDateTime = $startDate !== null ? new \DateTimeImmutable((string) $startDate) : null;
                $endDateTime = $endDate !== null ? new \DateTimeImmutable((string) $endDate) : null;
            } catch (\Exception $e) {
                Flash::set('error', 'Voucher dates are invalid.');
                $this->redirect('/merchant/vouchers');
                return;
            }

            if ($startDateTime !== null && $endDateTime !== null && $startDateTime > $endDateTime) {
                Flash::set('error', 'End date must be after or equal to start date.');
                $this->redirect('/merchant/vouchers');
                return;
            }
        }

        try {
            $voucherModel->update((int) $id, [
                'voucher_code' => $code,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'minimum_spend' => $minSpend,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'usage_limit' => $usageLimit,
                'status' => (string) $this->input('status', 'active'),
            ]);
        } catch (\PDOException $e) {
            if ((string) $e->getCode() === '23000') {
                Flash::set('error', 'Voucher code already exists for your store.');
                $this->redirect('/merchant/vouchers');
                return;
            }
            throw $e;
        }
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
