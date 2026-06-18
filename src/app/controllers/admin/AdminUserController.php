<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireRole('admin');
        $user = new User();
        $this->view('admin/users', [
            'title' => 'Users',
            'users' => $user->allNonAdmins(),
        ], 'layout/admin-layout');
    }

    public function updateStatus(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $user = new User();
        $target = $user->find((int) $id);
        if (!$target || $target['role'] === 'admin') {
            Flash::set('error', 'Admin accounts cannot be managed from this page.');
            $this->redirect('/admin/users');
        }

        $status = (string) $this->input('status', 'active');
        $user->update((int) $id, ['status' => $status]);
        Flash::set('success', 'User status updated.');
        $this->redirect('/admin/users');
    }

    public function updateRole(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $user = new User();
        $target = $user->find((int) $id);
        if (!$target || $target['role'] === 'admin') {
            Flash::set('error', 'Admin accounts cannot be managed from this page.');
            $this->redirect('/admin/users');
        }

        $role = (string) $this->input('role', 'customer');
        if (!in_array($role, ['customer', 'merchant'], true)) {
            Flash::set('error', 'Invalid role.');
            $this->redirect('/admin/users');
        }
        $user->update((int) $id, ['role' => $role]);
        Flash::set('success', 'User role updated.');
        $this->redirect('/admin/users');
    }
}
