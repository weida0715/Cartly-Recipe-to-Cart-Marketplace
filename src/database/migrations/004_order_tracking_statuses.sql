ALTER TABLE merchant_orders
  MODIFY status ENUM('pending','accepted','preparing','ready_to_deliver','out_for_delivery','delivered','completed','cancelled') NOT NULL DEFAULT 'pending';