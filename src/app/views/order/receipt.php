<?php
$downloadMode = $downloadMode ?? false;
$payment = is_array($payment ?? null) ? $payment : [];
$customerName = (string) (($order['customer_name_snapshot'] ?? '') ?: 'Cartly customer');
$customerEmail = (string) (($order['customer_email_snapshot'] ?? '') ?: 'Not recorded');
$subtotal = 0.0;
$discount = 0.0;
$delivery = 0.0;
foreach ($merchantOrders as $merchantOrder) {
    $subtotal += (float) ($merchantOrder['subtotal'] ?? 0);
    $discount += (float) ($merchantOrder['discount_amount'] ?? 0);
    $delivery += (float) ($merchantOrder['delivery_fee'] ?? 0);
}
if ($downloadMode): ?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($receiptNumber) ?> - Cartly receipt</title></head><body>
<?php endif; ?>
<style>
  .receipt-shell{max-width:860px;margin:0 auto;background:#fff;border:1px solid #d6ded8;padding:28px;color:#1f2937;font-family:Arial,sans-serif}
  .receipt-head,.receipt-meta,.receipt-total-row{display:flex;justify-content:space-between;gap:20px}.receipt-head{border-bottom:3px solid #2e7d32;padding-bottom:18px}.receipt-brand{color:#1b5e20;font-size:26px;font-weight:800}.receipt-meta{margin:22px 0}.receipt-meta>div{flex:1}.receipt-label{color:#6b7280;font-size:12px;text-transform:uppercase}.receipt-table{border-collapse:collapse;width:100%;margin:14px 0 24px}.receipt-table th,.receipt-table td{border-bottom:1px solid #e5e7eb;padding:10px;text-align:left}.receipt-table th:last-child,.receipt-table td:last-child{text-align:right}.receipt-store{background:#f5f7f6;font-weight:700}.receipt-totals{margin-left:auto;max-width:360px}.receipt-total-row{padding:6px 0}.receipt-grand{border-top:2px solid #1f2937;font-size:18px;font-weight:800;margin-top:6px;padding-top:12px}.receipt-note{border-top:1px solid #e5e7eb;color:#6b7280;margin-top:26px;padding-top:18px;text-align:center}.receipt-page-actions{display:flex;gap:10px;justify-content:center;margin:18px auto;max-width:860px;flex-wrap:wrap}@media(max-width:650px){.receipt-head,.receipt-meta{flex-direction:column}.receipt-shell{padding:18px}.receipt-table{font-size:13px}}@media print{.receipt-page-actions,.site-header,.site-footer{display:none!important}.receipt-shell{border:0;padding:0}}
</style>

<?php if (!$downloadMode): ?>
  <div class="receipt-page-actions">
    <button class="btn btn-primary" type="button" onclick="window.print()">Print or save as PDF</button>
    <a class="btn btn-outline" href="<?= BASE_URL ?>/orders/<?= (int) $order['order_id'] ?>/receipt/download">Download HTML receipt</a>
    <a class="btn btn-outline" href="<?= BASE_URL ?>/orders/<?= (int) $order['order_id'] ?>">Back to order</a>
  </div>
<?php endif; ?>

<article class="receipt-shell">
  <header class="receipt-head">
    <div><div class="receipt-brand">Cartly</div><div>Recipe-to-Cart Marketplace</div></div>
    <div><div class="receipt-label">Receipt number</div><strong><?= htmlspecialchars($receiptNumber) ?></strong></div>
  </header>

  <div class="receipt-meta">
    <div><div class="receipt-label">Billed to</div><strong><?= htmlspecialchars($customerName) ?></strong><br><?= htmlspecialchars($customerEmail) ?><br><?= nl2br(htmlspecialchars((string) ($order['shipping_address'] ?? ''))) ?></div>
    <div><div class="receipt-label">Order</div><strong>#<?= (int) $order['order_id'] ?></strong><br><?= htmlspecialchars((string) $order['created_at']) ?><br>Payment: <?= htmlspecialchars((string) ($order['payment_status'] ?? '')) ?></div>
    <div><div class="receipt-label">Transaction</div><strong><?= htmlspecialchars((string) ($payment['transaction_reference'] ?? 'Legacy order')) ?></strong><br><?= htmlspecialchars((string) ($payment['provider_name'] ?? $order['payment_method'] ?? 'Not recorded')) ?><br><?= htmlspecialchars((string) ($payment['masked_account'] ?? '')) ?></div>
  </div>

  <table class="receipt-table">
    <thead><tr><th>Description</th><th>Qty</th><th>Unit price</th><th>Amount</th></tr></thead>
    <tbody>
      <?php foreach ($merchantOrders as $merchantOrder): ?>
        <tr class="receipt-store"><td colspan="4"><?= htmlspecialchars((string) ($merchantOrder['store_name'] ?? 'Merchant')) ?></td></tr>
        <?php foreach (($merchantOrder['items'] ?? []) as $item): ?>
          <tr>
            <td><?= htmlspecialchars((string) ($item['product_name_snapshot'] ?? 'Item')) ?></td>
            <td><?= (int) ($item['quantity'] ?? 0) ?></td>
            <td>RM <?= number_format((float) ($item['unit_price'] ?? 0), 2) ?></td>
            <td>RM <?= number_format((float) ($item['subtotal'] ?? 0), 2) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="receipt-totals">
    <div class="receipt-total-row"><span>Subtotal</span><span>RM <?= number_format($subtotal, 2) ?></span></div>
    <div class="receipt-total-row"><span>Discount</span><span>- RM <?= number_format($discount, 2) ?></span></div>
    <div class="receipt-total-row"><span>Delivery</span><span>RM <?= number_format($delivery, 2) ?></span></div>
    <div class="receipt-total-row receipt-grand"><span>Total paid</span><span>RM <?= number_format((float) $order['total_amount'], 2) ?></span></div>
  </div>

  <footer class="receipt-note">This computer-generated receipt confirms the mock payment recorded by Cartly. No signature is required.</footer>
</article>
<?php if ($downloadMode): ?></body></html><?php endif; ?>
