<?php
declare(strict_types=1);
namespace App\Controllers\Merchant;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Store;

class MerchantStoreController extends Controller
{
    public function edit(): void
    {
        AuthHelper::requireRole('merchant');
        $store = (new Store())->byUser((int) AuthHelper::id());
        $this->view('merchant/store', [
            'title' => 'Store Profile',
            'store' => $store,
        ], 'layout/merchant-layout');
    }

    public function update(): void
    {
        AuthHelper::requireRole('merchant');
        $this->requireCsrf();
        $uid = (int) AuthHelper::id();
        $store = (new Store())->byUser($uid);
        $data = [
            'store_name'        => trim((string) $this->input('store_name', '')),
            'store_description' => (string) $this->input('store_description', ''),
            'contact_email'     => (string) $this->input('contact_email', ''),
            'contact_phone'     => (string) $this->input('contact_phone', ''),
            'store_address'     => (string) $this->input('store_address', ''),
            'opening_time'      => $this->input('opening_time') ?: null,
            'closing_time'      => $this->input('closing_time') ?: null,
        ];
        $sm = new Store();
        if ($store) {
            $sm->update((int) $store['store_id'], $data);
            Flash::set('success', 'Store updated.');
        } else {
            $data['user_id'] = $uid;
            $data['store_status'] = 'pending';
            $sm->insert($data);
            Flash::set('success', 'Store created — awaiting admin approval.');
        }
        $this->redirect('/merchant/store');
    }
}
