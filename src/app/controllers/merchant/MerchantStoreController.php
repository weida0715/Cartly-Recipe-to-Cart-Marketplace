<?php
declare(strict_types=1);
namespace App\Controllers\Merchant;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Helpers\FileUploadHelper;
use App\Helpers\Validator;
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
            'store_name' => trim((string) $this->input('store_name', '')),
            'store_description' => (string) $this->input('store_description', ''),
            'contact_email' => trim((string) $this->input('contact_email', '')),
            'contact_phone' => trim((string) $this->input('contact_phone', '')),
            'store_address' => trim((string) $this->input('store_address', '')),
            'opening_time' => $this->input('opening_time') ?: null,
            'closing_time' => $this->input('closing_time') ?: null,
        ];
        $v = new Validator($data);
        $v->required('store_name', 'Store name')
            ->required('contact_email', 'Contact email')->email('contact_email')
            ->phone('contact_phone', 'Contact phone')
            ->required('store_address', 'Address');
        if ($v->fails()) {
            Flash::set('error', reset($v->errors));
            $this->redirect('/merchant/store');
        }

        try {
            $logo = FileUploadHelper::image('store_logo', 'stores/logos');
        } catch (\RuntimeException $e) {
            Flash::set('error', $e->getMessage());
            $this->redirect('/merchant/store');
        }
        if ($logo !== null) {
            $data['store_logo'] = $logo;
        }

        $sm = new Store();
        try {
            if ($store) {
                $oldLogoPath = $store['store_logo'] ?? null;
                $updateSuccess = $sm->update((int) $store['store_id'], $data);
                if (!$updateSuccess) {
                    throw new \RuntimeException('Store update failed.');
                }
                if ($logo !== null && !empty($oldLogoPath)) {
                    FileUploadHelper::delete((string) $oldLogoPath);
                }
                Flash::set('success', 'Store updated.');
            } else {
                $data['user_id'] = $uid;
                $data['store_status'] = 'pending';
                if ($sm->insert($data) <= 0) {
                    throw new \RuntimeException('Store creation failed.');
                }
                Flash::set('success', 'Store created - awaiting admin approval.');
            }
        } catch (\Throwable $e) {
            if ($logo !== null) {
                FileUploadHelper::delete($logo);
            }
            Flash::set('error', 'Store could not be saved. Please try again.');
            $this->redirect('/merchant/store');
        }

        $this->redirect('/merchant/store');
    }
}
