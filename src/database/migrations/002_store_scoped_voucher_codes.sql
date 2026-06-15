-- Allow different stores to reuse the same public voucher code while keeping
-- each store's voucher codes unique.
ALTER TABLE vouchers
  DROP INDEX voucher_code,
  ADD UNIQUE KEY uniq_store_voucher_code (store_id, voucher_code);
