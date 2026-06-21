<?php
declare(strict_types=1);
namespace App\Controllers\Admin;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Helpers\FileUploadHelper;
use App\Models\Category;

class AdminCategoryController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireRole('admin');
        $this->view('admin/categories', [
            'title' => 'Categories',
            'cats' => (new Category())->all('category_name'),
        ], 'layout/admin-layout');
    }

    public function store(): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $name = trim((string) $this->input('category_name', ''));
        if ($name === '') {
            Flash::set('error', 'Category name is required.');
            $this->redirect('/admin/categories');
        }
        try {
            $icon = FileUploadHelper::image('category_icon', 'seeded/categories');
        } catch (\RuntimeException $e) {
            Flash::set('error', $e->getMessage());
            $this->redirect('/admin/categories');
        }
        if ($icon === null) {
            Flash::set('error', 'Category image is required.');
            $this->redirect('/admin/categories');
        }

        (new Category())->insert([
            'category_name' => $name,
            'category_icon' => $icon,
            'status' => 'active',
        ]);
        Flash::set('success', 'Category added.');
        $this->redirect('/admin/categories');
    }

    public function update(string $id): void
    {
        AuthHelper::requireRole('admin');
        $this->requireCsrf();
        $category = (new Category())->find((int) $id);
        if (!$category) {
            Flash::set('error', 'Category not found.');
            $this->redirect('/admin/categories');
        }
        $name = trim((string) $this->input('category_name', ''));
        if ($name === '') {
            Flash::set('error', 'Category name is required.');
            $this->redirect('/admin/categories');
        }

        $data = ['category_name' => $name];
        try {
            $icon = FileUploadHelper::image('category_icon', 'seeded/categories');
        } catch (\RuntimeException $e) {
            Flash::set('error', $e->getMessage());
            $this->redirect('/admin/categories');
        }
        if ($icon !== null) {
            $data['category_icon'] = $icon;
            if (
                !empty($category['category_icon']) &&
                str_contains((string) $category['category_icon'], '/') &&
                !str_starts_with((string) $category['category_icon'], 'seeded/')
            ) {
                FileUploadHelper::delete((string) $category['category_icon']);
            }
        }

        (new Category())->update((int) $id, $data);
        Flash::set('success', 'Category updated.');
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
