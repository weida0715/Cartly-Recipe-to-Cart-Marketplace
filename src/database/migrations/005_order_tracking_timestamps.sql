ALTER TABLE merchant_orders
  ADD COLUMN accepted_at DATETIME NULL AFTER status,
  ADD COLUMN preparing_at DATETIME NULL AFTER accepted_at,
  ADD COLUMN ready_to_deliver_at DATETIME NULL AFTER preparing_at,
  ADD COLUMN out_for_delivery_at DATETIME NULL AFTER ready_to_deliver_at,
  ADD COLUMN delivered_at DATETIME NULL AFTER out_for_delivery_at,
  ADD COLUMN completed_at DATETIME NULL AFTER delivered_at,
  ADD COLUMN cancelled_at DATETIME NULL AFTER completed_at;

UPDATE merchant_orders mo
JOIN orders o ON o.order_id = mo.order_id
SET
  accepted_at = CASE WHEN mo.status IN ('accepted','preparing','ready_to_deliver','out_for_delivery','delivered','completed') THEN o.created_at ELSE accepted_at END,
  preparing_at = CASE WHEN mo.status IN ('preparing','ready_to_deliver','out_for_delivery','delivered','completed') THEN o.created_at ELSE preparing_at END,
  ready_to_deliver_at = CASE WHEN mo.status IN ('ready_to_deliver','out_for_delivery','delivered','completed') THEN o.created_at ELSE ready_to_deliver_at END,
  out_for_delivery_at = CASE WHEN mo.status IN ('out_for_delivery','delivered','completed') THEN o.created_at ELSE out_for_delivery_at END,
  delivered_at = CASE WHEN mo.status IN ('delivered','completed') THEN o.created_at ELSE delivered_at END,
  completed_at = CASE WHEN mo.status = 'completed' THEN o.created_at ELSE completed_at END,
  cancelled_at = CASE WHEN mo.status = 'cancelled' THEN o.created_at ELSE cancelled_at END;