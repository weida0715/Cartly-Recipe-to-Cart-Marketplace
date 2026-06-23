-- Record every voucher applied to a merchant order while keeping the legacy
-- merchant_orders.voucher_id column for backward compatibility.
CREATE TABLE merchant_order_vouchers (
  merchant_order_voucher_id INT AUTO_INCREMENT PRIMARY KEY,
  merchant_order_id         INT NOT NULL,
  voucher_id                INT NULL,
  discount_amount           DECIMAL(10,2) NOT NULL DEFAULT 0,
  created_at                DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_merchant_order_voucher (merchant_order_id, voucher_id),
  FOREIGN KEY (merchant_order_id) REFERENCES merchant_orders(merchant_order_id) ON DELETE CASCADE,
  FOREIGN KEY (voucher_id)        REFERENCES vouchers(voucher_id) ON DELETE SET NULL
);

INSERT INTO merchant_order_vouchers (merchant_order_id, voucher_id, discount_amount)
SELECT merchant_order_id, voucher_id, discount_amount
FROM merchant_orders
WHERE voucher_id IS NOT NULL;
