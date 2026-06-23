<?php
declare(strict_types=1);
namespace App\Controllers\Order;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\CartVoucherSession;
use App\Helpers\CartPricing;
use App\Helpers\Flash;
use App\Helpers\Validator;
use App\Helpers\MockPaymentGateway;
use App\Models\Cart;
use App\Models\AppSetting;
use App\Models\CartItem;
use App\Models\Notification;
use App\Models\PaymentTransaction;
use App\Models\Voucher;
use App\Models\Product;

class CheckoutController extends Controller
{
    public function index(): void
    {
        AuthHelper::requireLogin();
        $userId = (int) AuthHelper::id();
        $cart = (new Cart())->findOrCreateForUser($userId);
        $cartId = (int) $cart['cart_id'];
        $items = (new CartItem())->detailed($cartId);
        if (!$items) {
            Flash::set('info', 'Your cart is empty.');
            $this->redirect('/cart');
        }

        $groups = $this->groupItems($items);
        $selected = CartVoucherSession::all($userId, $cartId);
        $voucherModel = new Voucher();
        $subtotal = 0.0;
        $discountTotal = 0.0;
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
                Flash::set('error', 'A voucher is no longer valid. Review your cart before checkout.');
                $this->redirect('/cart');
            }
            $group['applied_vouchers'] = $pricing['applied'];
            $group['discount_total'] = $pricing['discount_total'];
            $group['final_total'] = $pricing['final_total'];
            $subtotal += (float) $group['subtotal'];
            $discountTotal += (float) $pricing['discount_total'];
        }
        unset($group);
        $deliveryFee = round(count($groups) * (new AppSetting())->deliveryFee(), 2);
        $totalAfterDiscount = max(0, $subtotal - $discountTotal);
        $this->view('order/checkout', [
            'title' => 'Checkout',
            'user' => AuthHelper::user(),
            'groups' => $groups,
            'subtotal' => $subtotal,
            'discountTotal' => $discountTotal,
            'deliveryFee' => $deliveryFee,
            'total' => CartPricing::totalWithDelivery($totalAfterDiscount, $deliveryFee),
        ]);
    }

    public function place(): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $userId = (int) AuthHelper::id();

        $cartModel = new Cart();
        $cart = $cartModel->findOrCreateForUser($userId);
        $cartId = (int) $cart['cart_id'];
        $items = (new CartItem())->detailed($cartId);
        if (!$items) {
            Flash::set('error', 'Cart is empty.');
            $this->redirect('/cart');
        }

        $shipping = trim((string) $this->input('shipping_address', ''));
        $phone = trim((string) $this->input('contact_phone', ''));
        $payment = (string) $this->input('payment_method', 'card');

        $validator = new Validator([
            'shipping_address' => $shipping,
            'contact_phone' => $phone,
        ]);
        $validator->required('shipping_address', 'Shipping address')
            ->required('contact_phone', 'Contact phone')
            ->phone('contact_phone', 'Contact phone');
        if ($validator->fails()) {
            Flash::set('error', reset($validator->errors));
            $this->redirect('/checkout');
        }
        if (!in_array($payment, ['card', 'online_banking', 'ewallet'], true)) {
            Flash::set('error', 'Payment method is invalid.');
            $this->redirect('/checkout');
        }

        $groups = $this->groupItems($items);
        $selected = CartVoucherSession::all($userId, $cartId);
        $voucherModel = new Voucher();
        $productModel = new Product();
        $paymentModel = new PaymentTransaction();
        $paymentGateway = new MockPaymentGateway();
        $user = AuthHelper::user() ?? [];

        // Group items by store.
        $groups = [];
        foreach ($items as $it) {
            $sid = (int) $it['store_id'];
            $groups[$sid]['items'][] = $it;
            $groups[$sid]['subtotal'] = ($groups[$sid]['subtotal'] ?? 0) + (float) $it['unit_price'] * (int) $it['quantity'];
        }

        // Stock sanity.
        foreach ($items as $it) {
            if ((int) $it['stock_quantity'] < (int) $it['quantity']) {
                Flash::set('error', 'Insufficient stock for ' . $it['product_name'] . '.');
                $this->redirect('/cart');
            }
        }

        $deliveryFee = round(count($groups) * (new AppSetting())->deliveryFee(), 2);
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
                Flash::set('error', 'A voucher is no longer valid. Review your cart and try again.');
                $this->redirect('/cart');
            }
            $group['applied_vouchers'] = $pricing['applied'];
            $group['discount_total'] = $pricing['discount_total'];
            $group['final_total'] = $pricing['final_total'];
        }
        unset($group);

        $db = db();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare(
                "INSERT INTO orders (
                    user_id, total_amount, payment_method, payment_status, order_status,
                    shipping_address, contact_phone, customer_name_snapshot, customer_email_snapshot
                 ) VALUES (:u, 0, :pm, 'pending', 'pending', :sa, :ph, :customer_name, :customer_email)"
            );
            $stmt->execute([
                ':u' => $userId,
                ':pm' => $payment,
                ':sa' => $shipping,
                ':ph' => $phone,
                ':customer_name' => (string) ($user['full_name'] ?? ''),
                ':customer_email' => (string) ($user['email'] ?? ''),
            ]);
            $orderId = (int) $db->lastInsertId();
            $grandTotal = 0.0;

            foreach ($groups as $storeId => $group) {
                $transactionPricing = $voucherModel->resolveCodesForStore(
                    array_column($group['applied_vouchers'], 'voucher_code'),
                    (int) $storeId,
                    (float) $group['subtotal']
                );
                if ($transactionPricing['invalid']) {
                    throw new \DomainException('A voucher is no longer valid.');
                }
                $appliedVouchers = $transactionPricing['applied'];
                foreach ($appliedVouchers as $voucher) {
                    if (!$voucherModel->incrementIfAvailable((int) $voucher['voucher_id'])) {
                        throw new \DomainException('Voucher usage limit reached.');
                    }
                }

                $firstVoucherId = $appliedVouchers ? (int) $appliedVouchers[0]['voucher_id'] : null;
                $merchantTotal = CartPricing::merchantTotal(
                    (float) $group['subtotal'],
                    (float) $transactionPricing['discount_total'],
                    $deliveryFee
                );
                $merchantOrder = $db->prepare(
                    "INSERT INTO merchant_orders (order_id, store_id, subtotal, voucher_id, discount_amount, delivery_fee, final_amount, status)
                     VALUES (:o, :s, :sub, :v, :d, :df, :final, 'pending')"
                );
                $merchantOrder->execute([
                    ':o' => $orderId,
                    ':s' => $storeId,
                    ':sub' => (float) $group['subtotal'],
                    ':v' => $firstVoucherId,
                    ':d' => (float) $transactionPricing['discount_total'],
                    ':df' => $deliveryFee,
                    ':final' => $merchantTotal,
                ]);
                $merchantOrderId = (int) $db->lastInsertId();

                if ($appliedVouchers) {
                    $orderVoucher = $db->prepare(
                        'INSERT INTO merchant_order_vouchers (merchant_order_id, voucher_id, discount_amount)
                         VALUES (:merchant_order_id, :voucher_id, :discount_amount)'
                    );
                    foreach ($appliedVouchers as $voucher) {
                        $orderVoucher->execute([
                            ':merchant_order_id' => $merchantOrderId,
                            ':voucher_id' => (int) $voucher['voucher_id'],
                            ':discount_amount' => (float) $voucher['discount_amount'],
                        ]);
                    }
                }

                $orderItem = $db->prepare(
                    "INSERT INTO order_items (merchant_order_id, product_id, recipe_id, recipe_ingredient_id, product_name_snapshot, unit_price, quantity, subtotal)
                     VALUES (:mo, :p, :r, :ri, :pn, :up, :q, :sub)"
                );
                foreach ($group['items'] as $item) {
                    $lineSubtotal = (float) $item['unit_price'] * (int) $item['quantity'];
                    $orderItem->execute([
                        ':mo' => $merchantOrderId,
                        ':p' => (int) $item['product_id'],
                        ':r' => $item['recipe_id'] !== null ? (int) $item['recipe_id'] : null,
                        ':ri' => $item['recipe_ingredient_id'] !== null ? (int) $item['recipe_ingredient_id'] : null,
                        ':pn' => $item['product_name'],
                        ':up' => (float) $item['unit_price'],
                        ':q' => (int) $item['quantity'],
                        ':sub' => $lineSubtotal,
                    ]);
                    if (!$productModel->decrementStockIfAvailable(
                        (int) $item['product_id'],
                        (int) $item['quantity']
                    )) {
                        throw new \DomainException('Insufficient stock for ' . $item['product_name'] . '.');
                    }
                }
                $grandTotal += $merchantTotal;
            }

            $paymentResult = $paymentGateway->process($payment, $_POST, $grandTotal);
            $paymentModel->insert(['order_id' => $orderId] + $paymentResult);

            $receiptNumber = 'RCT-' . date('Ymd') . '-' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT);
            $db->prepare(
                "UPDATE orders
                 SET total_amount = :total, payment_status = 'paid', receipt_number = :receipt
                 WHERE order_id = :order_id"
            )->execute([
                ':total' => $grandTotal,
                ':receipt' => $receiptNumber,
                ':order_id' => $orderId,
            ]);

            $db->commit();
        } catch (\RuntimeException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            Flash::set('error', $e->getMessage());
            $this->redirect('/checkout');
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log('Checkout failed: ' . $e->getMessage());
            Flash::set('error', 'Checkout failed. Please try again.');
            $this->redirect('/cart');
        }

        $notificationModel = new Notification();
        foreach ($groups as $sid => $group) {
            $notificationModel->createForStore(
                (int) $sid,
                'info',
                'New order received',
                'A new store order was placed under order #' . $orderId . '.',
                '/merchant/orders'
            );
            foreach ($group['items'] as $item) {
                $freshProduct = $productModel->find((int) $item['product_id']);
                if ($freshProduct && (int) $freshProduct['stock_quantity'] <= 0) {
                    $notificationModel->createForStore(
                        (int) $sid,
                        'warning',
                        'Product out of stock',
                        $freshProduct['product_name'] . ' is now out of stock.',
                        '/merchant/products/' . (int) $freshProduct['product_id'] . '/edit'
                    );
                }
            }
        }

        CartVoucherSession::clear($userId, $cartId);
        Flash::set('success', 'Order placed.');
        $this->redirect('/orders/' . $orderId . '/confirmation');
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
