<?php
declare(strict_types=1);
namespace App\Controllers\Order;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\CartVoucherSession;
use App\Helpers\Flash;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Voucher;

class CartController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireLogin();
        $userId = (int) AuthHelper::id();
        $cart = (new Cart())->findOrCreateForUser($userId);
        $cartId = (int) $cart['cart_id'];
        $items = (new CartItem())->detailed($cartId);
        $groups = $this->groupItems($items);
        $selected = CartVoucherSession::all($userId, $cartId);
        $voucherModel = new Voucher();
        $subtotal = 0.0;
        $discountTotal = 0.0;
        $invalidRemoved = false;

        foreach ($groups as $storeId => &$group) {
            $pricing = $voucherModel->resolveCodesForStore(
                $selected[$storeId] ?? [],
                (int) $storeId,
                (float) $group['subtotal']
            );
            if ($pricing['invalid']) {
                CartVoucherSession::replaceStore(
                    $userId,
                    $cartId,
                    (int) $storeId,
                    array_column($pricing['applied'], 'voucher_code')
                );
                $invalidRemoved = true;
            }
            $group['available_vouchers'] = $voucherModel->availableForStoreSubtotal(
                (int) $storeId,
                (float) $group['subtotal']
            );
            $group['applied_vouchers'] = $pricing['applied'];
            $group['discount_total'] = $pricing['discount_total'];
            $group['final_total'] = $pricing['final_total'];
            $subtotal += (float) $group['subtotal'];
            $discountTotal += (float) $pricing['discount_total'];
        }
        unset($group);

        if ($invalidRemoved) {
            Flash::set('info', 'One or more vouchers were removed because they are no longer valid for the cart.');
        }

        $this->view('order/cart', [
            'title' => 'Your Cart - Cartly',
            'groups' => $groups,
            'subtotal' => $subtotal,
            'discountTotal' => $discountTotal,
            'total' => max(0, $subtotal - $discountTotal),
            'count' => count($items),
        ]);
    }

    public function add(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $productId = (int) $this->input('product_id');
        $qty = max(1, (int) $this->input('quantity', 1));
        $cart = (new Cart())->findOrCreateForUser((int) AuthHelper::id());
        $result = (new CartItem())->addManualWithinStock((int) $cart['cart_id'], $productId, $qty);
        if ($result === 'unavailable') {
            Flash::set('error', 'Product unavailable.');
            $this->redirect('/products');
        }
        if ($result === 'insufficient_stock') {
            Flash::set('error', 'Not enough stock.');
            $this->redirect('/products/' . $productId);
        }
        Flash::set('success', 'Added to cart.');
        $this->redirect('/cart');
    }

    public function applyVoucher(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $userId = (int) AuthHelper::id();
        $storeId = (int) $this->input('store_id', 0);
        $code = strtoupper(trim((string) $this->input('voucher_code', '')));
        if ($storeId <= 0 || $code === '' || strlen($code) > 50) {
            Flash::set('error', 'Enter a valid voucher code to apply.');
            $this->redirect('/cart');
        }

        $cart = (new Cart())->findOrCreateForUser($userId);
        $cartId = (int) $cart['cart_id'];
        $subtotal = 0.0;
        foreach ((new CartItem())->detailed($cartId) as $item) {
            if ((int) $item['store_id'] === $storeId) {
                $subtotal += (float) $item['unit_price'] * (int) $item['quantity'];
            }
        }
        if ($subtotal <= 0) {
            Flash::set('error', 'That merchant is not part of your cart.');
            $this->redirect('/cart');
        }

        $voucherModel = new Voucher();
        $voucher = $voucherModel->findValidForStore($code, $storeId, $subtotal);
        if (!$voucher) {
            Flash::set('error', 'This voucher is unavailable or does not meet the merchant subtotal.');
            $this->redirect('/cart');
        }

        $selected = CartVoucherSession::all($userId, $cartId)[$storeId] ?? [];
        $currentPricing = $voucherModel->resolveCodesForStore($selected, $storeId, $subtotal);
        $appliedCodes = array_column($currentPricing['applied'], 'voucher_code');
        CartVoucherSession::replaceStore($userId, $cartId, $storeId, $appliedCodes);
        if (in_array($code, $appliedCodes, true)) {
            Flash::set('info', 'This voucher is already applied.');
            $this->redirect('/cart');
        }
        if ((float) $currentPricing['final_total'] <= 0) {
            Flash::set('error', 'This merchant subtotal is already fully discounted.');
            $this->redirect('/cart');
        }

        $testPricing = $voucherModel->resolveCodesForStore(
            array_merge($appliedCodes, [$code]),
            $storeId,
            $subtotal
        );
        if (
            in_array($code, $testPricing['invalid'], true)
            || !in_array($code, array_column($testPricing['applied'], 'voucher_code'), true)
        ) {
            Flash::set('error', 'This voucher cannot be applied or provides no additional discount.');
            $this->redirect('/cart');
        }

        CartVoucherSession::add($userId, $cartId, $storeId, $code);
        Flash::set('success', 'Voucher ' . $code . ' applied.');
        $this->redirect('/cart');
    }

    public function removeVoucher(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $userId = (int) AuthHelper::id();
        $cart = (new Cart())->findOrCreateForUser($userId);
        CartVoucherSession::remove(
            $userId,
            (int) $cart['cart_id'],
            (int) $this->input('store_id', 0),
            (string) $this->input('voucher_code', '')
        );
        Flash::set('info', 'Voucher removed.');
        $this->redirect('/cart');
    }

    public function update(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $id = (int) $this->input('cart_item_id');
        $qty = max(1, (int) $this->input('quantity', 1));
        $cartItem = new CartItem();
        $item = $cartItem->findForUser($id, (int) AuthHelper::id());
        if (!$item) {
            Flash::set('error', 'Cart item not found.');
            $this->redirect('/cart');
        }
        if ($qty > (int) $item['stock_quantity']) {
            Flash::set('error', 'Only ' . (int) $item['stock_quantity'] . ' left in stock for ' . $item['product_name'] . '.');
            $this->redirect('/cart');
        }
        $cartItem->update($id, ['quantity' => $qty]);
        Flash::set('success', 'Cart updated.');
        $this->redirect('/cart');
    }

    public function remove(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $id = (int) $this->input('cart_item_id');
        $cartItem = new CartItem();
        $item = $cartItem->findForUser($id, (int) AuthHelper::id());
        if (!$item) {
            Flash::set('error', 'Cart item not found.');
            $this->redirect('/cart');
        }
        $cartItem->delete($id);
        Flash::set('info', 'Item removed.');
        $this->redirect('/cart');
    }

    public function clear(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $userId = (int) AuthHelper::id();
        $cart = (new Cart())->findOrCreateForUser($userId);
        $cartId = (int) $cart['cart_id'];
        (new Cart())->clear($cartId);
        CartVoucherSession::clear($userId, $cartId);
        Flash::set('info', 'Cart cleared.');
        $this->redirect('/cart');
    }

    private function groupItems(array $items): array
    {
        $groups = [];
        foreach ($items as $item) {
            $line = (float) $item['unit_price'] * (int) $item['quantity'];
            $storeId = (int) $item['store_id'];
            $groups[$storeId]['store_name'] = $item['store_name'];
            $groups[$storeId]['items'][] = $item + ['line_total' => $line];
            $groups[$storeId]['subtotal'] = ($groups[$storeId]['subtotal'] ?? 0) + $line;
        }
        return $groups;
    }
}
