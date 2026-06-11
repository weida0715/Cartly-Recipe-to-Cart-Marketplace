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
        $this->view('admin/users', [
            'title' => 'Users',
            'users' => (new User())->all('created_at DESC'),
        ], 'layout/admin-layout');
    }

    public function updateStatus(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $status = (string) $this->input('status', 'active');
        (new User())->update((int) $id, ['status' => $status]);
        Flash::set('success', 'User status updated.');
        $this->redirect('/admin/users');
    }

    public function updateRole(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $role = (string) $this->input('role', 'customer');
        if (!in_array($role, ['customer', 'merchant', 'admin'], true)) {
            Flash::set('error', 'Invalid role.');
            $this->redirect('/admin/users');
        }
        (new User())->update((int) $id, ['role' => $role]);
        Flash::set('success', 'User role updated.');
        $this->redirect('/admin/users');
    }
}
