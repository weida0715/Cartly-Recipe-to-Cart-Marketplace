-- Issue #79: mock payment transaction metadata without storing sensitive credentials.
ALTER TABLE orders
  ADD COLUMN customer_name_snapshot VARCHAR(100) NOT NULL DEFAULT '' AFTER contact_phone,
  ADD COLUMN customer_email_snapshot VARCHAR(100) NOT NULL DEFAULT '' AFTER customer_name_snapshot,
  ADD COLUMN receipt_number VARCHAR(40) NULL AFTER customer_email_snapshot,
  ADD UNIQUE KEY uniq_orders_receipt_number (receipt_number);

CREATE TABLE payment_transactions (
  payment_transaction_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id        INT NOT NULL UNIQUE,
  transaction_reference VARCHAR(50) NOT NULL UNIQUE,
  payment_method  ENUM('card','online_banking','ewallet') NOT NULL,
  provider_name   VARCHAR(80) NOT NULL,
  payer_name      VARCHAR(100) NOT NULL,
  masked_account  VARCHAR(80) NOT NULL,
  amount          DECIMAL(10,2) NOT NULL,
  status          ENUM('approved','failed') NOT NULL,
  processed_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- Existing orders predate receipt snapshots. Preserve usable fallback values.
UPDATE orders o
JOIN users u ON u.user_id = o.user_id
SET o.customer_name_snapshot = u.full_name,
    o.customer_email_snapshot = u.email,
    o.receipt_number = CONCAT('RCT-', DATE_FORMAT(o.created_at, '%Y%m%d'), '-', LPAD(o.order_id, 6, '0'))
WHERE o.receipt_number IS NULL;
