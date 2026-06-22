<?php
declare(strict_types=1);
namespace App\Controllers\Order;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Cart;
use App\Models\CartItem;

class CartController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireLogin();
        $cart = (new Cart())->findOrCreateForUser((int) AuthHelper::id());
        $items = (new CartItem())->detailed((int) $cart['cart_id']);
        // group by store
        $groups = [];
        $total = 0.0;
        foreach ($items as $it) {
            $line = (float) $it['unit_price'] * (int) $it['quantity'];
            $total += $line;
            $sid = (int) $it['store_id'];
            $groups[$sid]['store_name'] = $it['store_name'];
            $groups[$sid]['items'][] = $it + ['line_total' => $line];
            $groups[$sid]['subtotal'] = ($groups[$sid]['subtotal'] ?? 0) + $line;
        }
        $this->view('order/cart', [
            'title'  => 'Your Cart · Cartly',
            'groups' => $groups,
            'total'  => $total,
            'count'  => count($items),
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
        (new CartItem())->delete($id);
        Flash::set('info', 'Item removed.');
        $this->redirect('/cart');
    }

    public function clear(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $cart = (new Cart())->findOrCreateForUser((int) AuthHelper::id());
        (new Cart())->clear((int) $cart['cart_id']);
        Flash::set('info', 'Cart cleared.');
        $this->redirect('/cart');
    }
}
