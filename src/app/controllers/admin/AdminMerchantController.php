<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Store;
use App\Models\Notification;
use App\Models\User;

class AdminMerchantController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireRole('admin');
        $storeModel = new Store();
        $this->view('admin/merchant-approval', [
            'title' => 'Merchant Approval',
            'pending' => $storeModel->pending(),
            'approvedHistory' => $storeModel->approvedRequestHistory(),
        ], 'layout/admin-layout');
    }

    public function approve(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $storeModel = new Store();
        $store = $storeModel->find((int) $id);
        if (!$store || (string) $store['store_status'] !== 'pending') {
            Flash::set('error', 'Pending merchant request not found.');
            $this->redirect('/admin/merchants');
        }
        $db = db();
        try {
            $db->beginTransaction();
            (new User())->update((int) $store['user_id'], ['role' => 'merchant']);
            $storeModel->recordReview((int) $id, 'approved', '');
            $db->commit();
        } catch (\Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log('Merchant approval failed: ' . $error->getMessage());
            Flash::set('error', 'Merchant request could not be approved.');
            $this->redirect('/admin/merchants');
        }
        (new Notification())->createForUser(
            (int) $store['user_id'],
            'success',
            'Merchant request approved',
            'Your store request was approved. You can now use the merchant portal.',
            '/merchant'
        );
        Flash::set('success', 'Merchant approved.');
        $this->redirect('/admin/merchants');
    }

    public function reject(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $storeModel = new Store();
        $store = $storeModel->find((int) $id);
        if (!$store || (string) $store['store_status'] !== 'pending') {
            Flash::set('error', 'Pending merchant request not found.');
            $this->redirect('/admin/merchants');
        }
        $note = trim((string) $this->input('admin_note', ''));
        if ($note === '') {
            Flash::set('error', 'A rejection reason is required.');
            $this->redirect('/admin/merchants');
        }
        $storeModel->recordReview((int) $id, 'rejected', $note);
        (new Notification())->createForUser(
            (int) $store['user_id'],
            'warning',
            'Merchant request rejected',
            'Your store request was rejected. Reason: ' . $note,
            '/dashboard'
        );
        Flash::set('info', 'Merchant rejected.');
        $this->redirect('/admin/merchants');
    }

    public function close(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $storeModel = new Store();
        $store = $storeModel->find((int) $id);
        if (!$store || (string) $store['store_status'] !== 'approved') {
            Flash::set('error', 'Approved merchant store not found.');
            $this->redirect('/admin/merchants');
        }
        $note = trim((string) $this->input('admin_note', ''));
        if ($note === '') {
            Flash::set('error', 'A closure reason is required.');
            $this->redirect('/admin/merchants');
        }
        $storeModel->update((int) $id, [
            'store_status' => 'closed',
            'admin_note' => $note,
        ]);
        Flash::set('info', 'Store closed.');
        $this->redirect('/admin/merchants');
    }
}
