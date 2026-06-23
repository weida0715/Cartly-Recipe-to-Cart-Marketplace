-- Add global application settings managed by administrators.
CREATE TABLE IF NOT EXISTS application_settings (
  setting_key   VARCHAR(100) PRIMARY KEY,
  setting_value VARCHAR(255) NOT NULL,
  updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT IGNORE INTO application_settings (setting_key, setting_value)
VALUES ('delivery_fee_per_store', '2.00');
