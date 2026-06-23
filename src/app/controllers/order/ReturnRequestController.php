<?php
declare(strict_types=1);

namespace App\Controllers\Order;

use App\Helpers\AuthHelper;
use App\Helpers\Controller;
use App\Helpers\Flash;
use App\Models\Notification;
use App\Models\ReturnRequest;
use App\Models\Store;

class ReturnRequestController extends Controller
{
    public function store(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $model = new ReturnRequest();
        $item = $model->eligibleItemForCustomer((int) $id, (int) AuthHelper::id());
        $type = (string) $this->input('request_type', '');
        $reason = trim((string) $this->input('reason', ''));
        $quantity = (int) $this->input('quantity', 0);

        if (!$item || (string) $item['merchant_order_status'] !== 'completed') {
            Flash::set('error', 'Returns and refunds are available only after the order is completed.');
            $this->redirect('/orders');
        }
        if (!empty($item['return_request_id'])) {
            Flash::set('info', 'A request already exists for this item.');
            $this->redirect('/orders/' . (int) $item['order_id']);
        }
        if (!in_array($type, ['refund', 'return'], true) || $reason === '' || strlen($reason) > 1000
            || $quantity < 1 || $quantity > (int) $item['quantity']) {
            Flash::set('error', 'Choose a valid request type, quantity, and reason.');
            $this->redirect('/orders/' . (int) $item['order_id']);
        }

        try {
            $requestId = $model->createRequest([
                'order_item_id' => (int) $item['order_item_id'],
                'merchant_order_id' => (int) $item['merchant_order_id'],
                'user_id' => (int) AuthHelper::id(),
                'store_id' => (int) $item['store_id'],
                'request_type' => $type,
                'reason' => $reason,
                'quantity' => $quantity,
                'requested_amount' => round((float) $item['unit_price'] * $quantity, 2),
                'status' => 'pending',
            ]);
            (new Notification())->createForStore(
                (int) $item['store_id'],
                'warning',
                'New return or refund request',
                'A customer requested a ' . $type . ' for ' . $item['product_name_snapshot'] . '.',
                '/merchant/orders#return-request-' . $requestId
            );
        } catch (\Throwable) {
            Flash::set('error', 'The request could not be submitted. Please try again.');
            $this->redirect('/orders/' . (int) $item['order_id']);
        }

        Flash::set('success', 'Your request was sent to the merchant.');
        $this->redirect('/orders/' . (int) $item['order_id']);
    }

    public function decide(string $id): void
    {
        AuthHelper::requireRole('merchant');
        $this->requireCsrf();
        $store = (new Store())->byUser((int) AuthHelper::id());
        if (!$store) {
            $this->redirect('/merchant/store');
        }
        $model = new ReturnRequest();
        $request = $model->findForStore((int) $id, (int) $store['store_id']);
        if (!$request || (string) $request['status'] !== 'pending') {
            Flash::set('error', 'Pending request not found.');
            $this->redirect('/merchant/orders');
        }

        $decision = (string) $this->input('decision', '');
        $note = trim((string) $this->input('merchant_note', ''));
        $refundAmount = round((float) $this->input('refund_amount', 0), 2);
        $status = match ($decision) {
            'refund' => 'refunded',
            'return' => 'return_approved',
            'reject' => 'rejected',
            default => '',
        };
        if (strlen($note) > 1000 || $status === ''
            || ($status !== 'rejected' && ($refundAmount <= 0 || $refundAmount > (float) $request['requested_amount']))) {
            Flash::set('error', 'Enter a valid decision and refund amount within the item total.');
            $this->redirect('/merchant/orders#return-request-' . (int) $id);
        }
        if ($status === 'rejected' && $note === '') {
            Flash::set('error', 'A rejection reason is required.');
            $this->redirect('/merchant/orders#return-request-' . (int) $id);
        }

        if (!$model->decide((int) $id, $status, $refundAmount, $note)) {
            Flash::set('error', 'The request status changed before it could be updated.');
            $this->redirect('/merchant/orders');
        }
        $message = match ($status) {
            'refunded' => 'Your refund was approved for RM ' . number_format($refundAmount, 2) . '.',
            'return_approved' => 'Your return was approved. Arrange delivery and mark it as shipped.',
            default => 'Your request was rejected. ' . $note,
        };
        (new Notification())->createForUser(
            (int) $request['user_id'],
            $status === 'rejected' ? 'warning' : 'success',
            'Return request updated',
            $message,
            '/orders/' . (int) $request['order_id']
        );
        Flash::set('success', 'Request updated.');
        $this->redirect('/merchant/orders#return-request-' . (int) $id);
    }

    public function ship(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $model = new ReturnRequest();
        $request = $model->findForCustomer((int) $id, (int) AuthHelper::id());
        if (!$request || !$model->markReturnShipped((int) $id, (int) AuthHelper::id())) {
            Flash::set('error', 'This return cannot be marked as shipped.');
            $this->redirect('/orders');
        }
        (new Notification())->createForUser(
            (int) $request['merchant_user_id'],
            'info',
            'Return shipment on the way',
            'The customer marked the return for ' . $request['product_name_snapshot'] . ' as shipped.',
            '/merchant/orders#return-request-' . (int) $id
        );
        Flash::set('success', 'Return marked as shipped.');
        $this->redirect('/orders/' . (int) $request['order_id']);
    }

    public function receive(string $id): void
    {
        AuthHelper::requireRole('merchant');
        $this->requireCsrf();
        $store = (new Store())->byUser((int) AuthHelper::id());
        if (!$store) {
            $this->redirect('/merchant/store');
        }
        $model = new ReturnRequest();
        $request = $model->findForStore((int) $id, (int) $store['store_id']);
        if (!$request || !$model->completeReturn((int) $id, (int) $store['store_id'])) {
            Flash::set('error', 'This return cannot be completed.');
            $this->redirect('/merchant/orders');
        }
        (new Notification())->createForUser(
            (int) $request['user_id'],
            'success',
            'Returned item received',
            'The merchant received your returned item and recorded a refund of RM ' . number_format((float) $request['refund_amount'], 2) . '.',
            '/orders/' . (int) $request['order_id']
        );
        Flash::set('success', 'Return received and refund recorded.');
        $this->redirect('/merchant/orders#return-request-' . (int) $id);
    }
}
