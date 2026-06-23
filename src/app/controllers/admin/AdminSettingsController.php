<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Helpers\AuthHelper;
use App\Helpers\Controller;
use App\Helpers\Flash;
use App\Models\AppSetting;

class AdminSettingsController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireRole('admin');
        $this->view('admin/settings', [
            'title' => 'Platform Settings',
            'deliveryFee' => (new AppSetting())->deliveryFee(),
        ], 'layout/admin-layout');
    }

    public function update(): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $rawFee = trim((string) $this->input('delivery_fee', ''));
        if (
            $rawFee === ''
            || preg_match('/^\d{1,3}(?:\.\d{1,2})?$/', $rawFee) !== 1
            || (float) $rawFee > 999.99
        ) {
            Flash::set('error', 'Delivery fee must be between RM 0.00 and RM 999.99.');
            $this->redirect('/admin/settings');
        }

        $fee = number_format((float) $rawFee, 2, '.', '');
        (new AppSetting())->set(AppSetting::DELIVERY_FEE_KEY, $fee);
        Flash::set('success', 'Delivery fee updated to RM ' . $fee . ' per merchant order.');
        $this->redirect('/admin/settings');
    }
}