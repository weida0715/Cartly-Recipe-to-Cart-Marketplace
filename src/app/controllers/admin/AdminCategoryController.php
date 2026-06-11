<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Category;

class AdminCategoryController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireRole('admin');
        $this->view('admin/categories', [
            'title' => 'Categories',
            'cats'  => (new Category())->all('category_name'),
        ], 'layout/admin-layout');
    }

    public function store(): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        (new Category())->insert([
            'category_name' => trim((string) $this->input('category_name', '')),
            'category_icon' => (string) $this->input('category_icon', ''),
            'status'        => 'active',
        ]);
        Flash::set('success', 'Category added.');
        $this->redirect('/admin/categories');
    }

    public function delete(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        (new Category())->update((int) $id, ['status' => 'inactive']);
        Flash::set('info', 'Category deactivated.');
        $this->redirect('/admin/categories');
    }
}
