-- Record when an administrator approves or rejects a merchant request.
ALTER TABLE stores
  ADD COLUMN IF NOT EXISTS reviewed_at DATETIME NULL AFTER created_at;

-- Existing approved/closed seeded stores predate this audit field. Their
-- request date remains available as the history fallback in the application.
