<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Store;
use App\Models\Notification;

class AdminMerchantController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireRole('admin');
        $this->view('admin/merchant-approval', [
            'title' => 'Merchant Approval',
            'pending' => (new Store())->pending(),
        ], 'layout/admin-layout');
    }

    public function approve(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $store = (new Store())->find((int) $id);
        if ($store) {
            (new \App\Models\User())->update((int) $store['user_id'], ['role' => 'merchant']);
        }
        (new Store())->update((int) $id, ['store_status' => 'approved', 'admin_note' => '']);
        if ($store) {
            (new Notification())->createForUser(
                (int) $store['user_id'],
                'success',
                'Merchant request approved',
                'Your store request was approved. You can now use the merchant portal.',
                '/merchant'
            );
        }
        Flash::set('success', 'Merchant approved.');
        $this->redirect('/admin/merchants');
    }

    public function reject(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $note = (string) $this->input('admin_note', '');
        $store = (new Store())->find((int) $id);
        (new Store())->update((int) $id, ['store_status' => 'rejected', 'admin_note' => $note]);
        if ($store) {
            (new Notification())->createForUser(
                (int) $store['user_id'],
                'warning',
                'Merchant request rejected',
                'Your store request was rejected.' . ($note !== '' ? ' Reason: ' . $note : ''),
                '/dashboard'
            );
        }
        Flash::set('info', 'Merchant rejected.');
        $this->redirect('/admin/merchants');
    }

    public function close(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $note = trim((string) $this->input('admin_note', 'Closed by admin.'));
        (new Store())->update((int) $id, ['store_status' => 'closed', 'admin_note' => $note]);
        Flash::set('info', 'Store closed.');
        $this->redirect('/admin/merchants');
    }
}
