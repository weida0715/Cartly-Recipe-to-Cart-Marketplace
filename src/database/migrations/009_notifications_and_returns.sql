-- Issue #74: persistent user notifications.
CREATE TABLE notifications (
  notification_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT NOT NULL,
  type            VARCHAR(40) NOT NULL DEFAULT 'info',
  title           VARCHAR(150) NOT NULL,
  message         TEXT NOT NULL,
  action_url      VARCHAR(255) NULL,
  is_read         TINYINT(1) NOT NULL DEFAULT 0,
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notifications_user_read (user_id, is_read, created_at),
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Issue #77: item-level return and refund requests.
ALTER TABLE orders
  MODIFY payment_status ENUM('pending','paid','failed','partially_refunded','refunded') NOT NULL DEFAULT 'pending';

CREATE TABLE return_requests (
  return_request_id INT AUTO_INCREMENT PRIMARY KEY,
  order_item_id     INT NOT NULL UNIQUE,
  merchant_order_id INT NOT NULL,
  user_id           INT NOT NULL,
  store_id          INT NOT NULL,
  request_type      ENUM('refund','return') NOT NULL,
  reason            TEXT NOT NULL,
  quantity          INT NOT NULL,
  requested_amount  DECIMAL(10,2) NOT NULL,
  refund_amount     DECIMAL(10,2) NULL,
  status            ENUM('pending','refund_approved','return_approved','return_shipped','refunded','rejected') NOT NULL DEFAULT 'pending',
  merchant_note     TEXT,
  created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
  decided_at        DATETIME NULL,
  return_shipped_at DATETIME NULL,
  resolved_at       DATETIME NULL,
  FOREIGN KEY (order_item_id) REFERENCES order_items(order_item_id) ON DELETE CASCADE,
  FOREIGN KEY (merchant_order_id) REFERENCES merchant_orders(merchant_order_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (store_id) REFERENCES stores(store_id) ON DELETE CASCADE
);
