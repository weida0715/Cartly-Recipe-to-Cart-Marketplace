<?php
declare(strict_types=1);
namespace App\Controllers\Order;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\CartPricing;
use App\Helpers\Flash;
use App\Helpers\Validator;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Voucher;
use App\Models\Product;

class CheckoutController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireLogin();
        $cart = (new Cart())->findOrCreateForUser((int) AuthHelper::id());
        $items = (new CartItem())->detailed((int) $cart['cart_id']);
        if (!$items) {
            Flash::set('info', 'Your cart is empty.');
            $this->redirect('/cart');
        }
        $groups = [];
        $total = 0;
        foreach ($items as $it) {
            $line = (float) $it['unit_price'] * (int) $it['quantity'];
            $total += $line;
            $sid = (int) $it['store_id'];
            $groups[$sid]['store_name'] = $it['store_name'];
            $groups[$sid]['items'][] = $it + ['line_total' => $line];
            $groups[$sid]['subtotal'] = ($groups[$sid]['subtotal'] ?? 0) + $line;
        }
        $voucherModel = new Voucher();
        foreach ($groups as $sid => &$group) {
            $group['vouchers'] = $voucherModel->availableForStoreSubtotal((int) $sid, (float) $group['subtotal']);
        }
        unset($group);
        $deliveryFee = CartPricing::estimatedDeliveryFee($groups);
        $this->view('order/checkout', [
            'title' => 'Checkout',
            'user' => AuthHelper::user(),
            'groups' => $groups,
            'subtotal' => $total,
            'deliveryFee' => $deliveryFee,
            'total' => CartPricing::totalWithDelivery((float) $total, $deliveryFee),
        ]);
    }

    public function place(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $userId = (int) AuthHelper::id();

        $cartModel = new Cart();
        $cart = $cartModel->findOrCreateForUser($userId);
        $items = (new CartItem())->detailed((int) $cart['cart_id']);
        if (!$items) {
            Flash::set('error', 'Cart is empty.');
            $this->redirect('/cart');
        }

        $shipping = trim((string) $this->input('shipping_address', ''));
        $phone = trim((string) $this->input('contact_phone', ''));
        $payment = (string) $this->input('payment_method', 'simulated');
        $vouchers = $_POST['voucher'] ?? []; // [storeId => code]

        $v = new Validator([
            'shipping_address' => $shipping,
            'contact_phone' => $phone,
        ]);
        $v->required('shipping_address', 'Shipping address')
            ->required('contact_phone', 'Contact phone')
            ->phone('contact_phone', 'Contact phone');
        if ($v->fails()) {
            Flash::set('error', reset($v->errors));
            $this->redirect('/checkout');
        }

        $db = db();
        $voucherModel = new Voucher();
        $productModel = new Product();

        // Group items by store
        $groups = [];
        foreach ($items as $it) {
            $sid = (int) $it['store_id'];
            $groups[$sid]['items'][] = $it;
            $groups[$sid]['subtotal'] = ($groups[$sid]['subtotal'] ?? 0) + (float) $it['unit_price'] * (int) $it['quantity'];
        }

        // Stock sanity
        foreach ($items as $it) {
            if ((int) $it['stock_quantity'] < (int) $it['quantity']) {
                Flash::set('error', 'Insufficient stock for ' . $it['product_name'] . '.');
                $this->redirect('/cart');
            }
        }

        try {
            $db->beginTransaction();

            // Parent order placeholder; total updated after merchant order discounts
            $stmt = $db->prepare(
                "INSERT INTO orders (user_id, total_amount, payment_method, payment_status, order_status, shipping_address, contact_phone)
                 VALUES (:u, 0, :pm, 'paid', 'pending', :sa, :ph)"
            );
            $stmt->execute([':u' => $userId, ':pm' => $payment, ':sa' => $shipping, ':ph' => $phone]);
            $orderId = (int) $db->lastInsertId();

            $grandTotal = 0.0;

            foreach ($groups as $sid => $g) {
                $subtotal = (float) $g['subtotal'];
                $deliveryFee = CartPricing::deliveryFeePerStore();
                $voucherId = null;
                $discount = 0.0;
                $code = trim((string) ($vouchers[$sid] ?? ''));
                if ($code !== '') {
                    $voucher = $voucherModel->findValidForStore($code, (int) $sid, $subtotal);
                    if (!$voucher) {
                        if ($db->inTransaction()) {
                            $db->rollBack();
                        }
                        $storeName = (string) ($g['items'][0]['store_name'] ?? 'one of the stores');
                        Flash::set('error', 'Invalid voucher code for ' . $storeName . '.');
                        $this->redirect('/checkout');
                    }
                    $discount = $voucherModel->computeDiscount($voucher, $subtotal);
                    $voucherId = (int) $voucher['voucher_id'];
                    if (!$voucherModel->incrementIfAvailable($voucherId)) {
                        if ($db->inTransaction()) {
                            $db->rollBack();
                        }
                        Flash::set('error', 'Voucher usage limit reached. Please remove the voucher and try again.');
                        $this->redirect('/checkout');
                    }
                }
                $moStmt = $db->prepare(
                    "INSERT INTO merchant_orders (order_id, store_id, subtotal, voucher_id, discount_amount, delivery_fee, final_amount, status)
                     VALUES (:o, :s, :sub, :v, :d, :df, :final, 'pending')"
                );
                $finalAmount = max(0, $subtotal - $discount) + $deliveryFee;
                $moStmt->execute([
                    ':o' => $orderId,
                    ':s' => $sid,
                    ':sub' => $subtotal,
                    ':v' => $voucherId,
                    ':d' => $discount,
                    ':df' => $deliveryFee,
                    ':final' => $finalAmount,
                ]);
                $moId = (int) $db->lastInsertId();

                $oiStmt = $db->prepare(
                    "INSERT INTO order_items (merchant_order_id, product_id, recipe_id, recipe_ingredient_id, product_name_snapshot, unit_price, quantity, subtotal)
                     VALUES (:mo, :p, :r, :ri, :pn, :up, :q, :sub)"
                );
                foreach ($g['items'] as $it) {
                    $lineSubtotal = (float) $it['unit_price'] * (int) $it['quantity'];
                    $oiStmt->execute([
                        ':mo' => $moId,
                        ':p' => (int) $it['product_id'],
                        ':r' => $it['recipe_id'] !== null ? (int) $it['recipe_id'] : null,
                        ':ri' => $it['recipe_ingredient_id'] !== null ? (int) $it['recipe_ingredient_id'] : null,
                        ':pn' => $it['product_name'],
                        ':up' => (float) $it['unit_price'],
                        ':q' => (int) $it['quantity'],
                        ':sub' => $lineSubtotal,
                    ]);
                    $productModel->decrementStock((int) $it['product_id'], (int) $it['quantity']);
                }
                $grandTotal += $finalAmount;
            }

            $db->prepare("UPDATE orders SET total_amount = :t WHERE order_id = :o")
                ->execute([':t' => $grandTotal, ':o' => $orderId]);

            $cartModel->clear((int) $cart['cart_id']);

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            Flash::set('error', 'Checkout failed: ' . $e->getMessage());
            $this->redirect('/cart');
        }

        Flash::set('success', 'Order placed.');
        $this->redirect('/orders/' . $orderId . '/confirmation');
    }
}
