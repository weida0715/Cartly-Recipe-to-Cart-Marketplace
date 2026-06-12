<?php
declare(strict_types=1);
namespace App\Controllers\Customer;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Models\Order;
use App\Models\Recipe;
use App\Models\SavedRecipe;
use App\Models\User;
use App\Models\Store;
use App\Helpers\Flash;

class CustomerDashboardController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireLogin();
        $uid = (int) AuthHelper::id();
        $this->view('customer/dashboard', [
            'title' => 'My Dashboard',
            'orders' => array_slice((new Order())->historyForUser($uid), 0, 5),
            'recipes' => array_slice((new Recipe())->byUser($uid), 0, 5),
            'storeRequest' => (new Store())->byUser($uid),
        ]);
    }

    public function savedRecipes(): void
    {
        AuthHelper::requireLogin();
        $this->view('customer/saved-recipes', [
            'title' => 'Saved Recipes',
            'recipes' => (new SavedRecipe())->forUser((int) AuthHelper::id()),
        ]);
    }

    public function profile(): void
    {
        AuthHelper::requireLogin();
        $this->view('customer/profile', [
            'title' => 'My Profile',
            'user' => (new User())->find((int) AuthHelper::id()),
        ]);
    }

    public function updateProfile(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $userId = (int) AuthHelper::id();
        $data = [
            'username' => trim((string) $this->input('username', '')),
            'full_name' => trim((string) $this->input('full_name', '')),
            'email' => trim((string) $this->input('email', '')),
            'phone' => trim((string) $this->input('phone', '')),
        ];
        if ($data['username'] === '' || $data['full_name'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Flash::set('error', 'Username, full name, and valid email are required.');
            $this->redirect('/profile');
        }
        $userModel = new User();
        if ($userModel->emailOrUsernameTakenByAnother($data['email'], $data['username'], $userId)) {
            Flash::set('error', 'Username or email is already used by another account.');
            $this->redirect('/profile');
        }
        $password = (string) $this->input('password', '');
        $confirm = (string) $this->input('confirm', '');
        if ($password !== '') {
            if (strlen($password) < 6 || $password !== $confirm) {
                Flash::set('error', 'New password must be at least 6 characters and match confirmation.');
                $this->redirect('/profile');
            }
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $userModel->update($userId, $data);
        $fresh = $userModel->find($userId);
        if ($fresh) {
            AuthHelper::login($fresh);
        }
        Flash::set('success', 'Profile updated.');
        $this->redirect('/profile');
    }

    public function requestMerchant(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();

        if (AuthHelper::role() !== 'customer') {
            Flash::set('error', 'Only customers can request a store.');
            $this->redirect('/dashboard');
        }

        $storeModel = new Store();
        $existing = $storeModel->byUser((int) AuthHelper::id());
        if ($existing) {
            Flash::set('info', 'You already have a store request/profile.');
            $this->redirect('/dashboard');
        }

        $data = [
            'user_id' => (int) AuthHelper::id(),
            'store_name' => trim((string) $this->input('store_name', '')),
            'store_description' => (string) $this->input('store_description', ''),
            'contact_email' => trim((string) $this->input('contact_email', '')),
            'contact_phone' => trim((string) $this->input('contact_phone', '')),
            'store_address' => trim((string) $this->input('store_address', '')),
            'opening_time' => $this->input('opening_time') ?: null,
            'closing_time' => $this->input('closing_time') ?: null,
            'store_status' => 'pending',
            'admin_note' => null,
            'rating' => 0,
        ];

        if ($data['store_name'] === '' || $data['contact_email'] === '' || $data['store_address'] === '') {
            Flash::set('error', 'Store name, email, and address are required.');
            $this->redirect('/dashboard');
        }

        $storeModel->insert($data);
        Flash::set('success', 'Store request submitted. Waiting for admin approval.');
        $this->redirect('/dashboard');
    }
}
