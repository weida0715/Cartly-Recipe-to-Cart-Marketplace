-- Cartly schema (MySQL 8 / MariaDB 10.4+)
-- Import via phpMyAdmin or: mysql -u root cartly < schema.sql
CREATE DATABASE IF NOT EXISTS cartly CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cartly;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS reports;
DROP TABLE IF EXISTS application_settings;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS merchant_order_vouchers;
DROP TABLE IF EXISTS merchant_orders;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS vouchers;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS saved_recipes;
DROP TABLE IF EXISTS recipe_ingredients;
DROP TABLE IF EXISTS recipes;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS ingredients;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS stores;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;
CREATE TABLE application_settings (
  setting_key   VARCHAR(100) PRIMARY KEY,
  setting_value VARCHAR(255) NOT NULL,
  updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE users (
  user_id    INT AUTO_INCREMENT PRIMARY KEY,
  username   VARCHAR(50) UNIQUE NOT NULL,
  full_name  VARCHAR(100) NOT NULL,
  email      VARCHAR(100) UNIQUE NOT NULL,
  phone      VARCHAR(20),
  password   VARCHAR(255) NOT NULL,
  role       ENUM('customer','merchant','admin') NOT NULL DEFAULT 'customer',
  status     ENUM('active','inactive','deactivated') NOT NULL DEFAULT 'active',
  reset_token_hash VARCHAR(255) NULL,
  reset_token_expires_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE stores (
  store_id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id           INT NOT NULL,
  store_name        VARCHAR(100) NOT NULL,
  store_description TEXT,
  store_logo        VARCHAR(255),
  contact_email     VARCHAR(100),
  contact_phone     VARCHAR(20),
  store_address     TEXT,
  opening_time      TIME,
  closing_time      TIME,
  store_status      ENUM('pending','approved','rejected','closed') NOT NULL DEFAULT 'pending',
  admin_note        TEXT,
  rating            DECIMAL(3,2) DEFAULT 0.00,
  created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
  reviewed_at       DATETIME NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
CREATE TABLE categories (
  category_id   INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL,
  category_icon VARCHAR(255),
  status        ENUM('active','inactive') NOT NULL DEFAULT 'active'
);
CREATE TABLE ingredients (
  ingredient_id   INT AUTO_INCREMENT PRIMARY KEY,
  ingredient_name VARCHAR(100) UNIQUE NOT NULL,
  base_unit       VARCHAR(30)
);
CREATE TABLE products (
  product_id       INT AUTO_INCREMENT PRIMARY KEY,
  store_id         INT NOT NULL,
  category_id      INT,
  ingredient_id    INT,
  product_name     VARCHAR(150) NOT NULL,
  description      TEXT,
  price            DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock_quantity   INT NOT NULL DEFAULT 0,
  package_quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
  package_unit     VARCHAR(30),
  image            VARCHAR(255),
  rating           DECIMAL(3,2) DEFAULT 0.00,
  status           ENUM('active','inactive','out_of_stock') NOT NULL DEFAULT 'active',
  created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (store_id)      REFERENCES stores(store_id) ON DELETE CASCADE,
  FOREIGN KEY (category_id)   REFERENCES categories(category_id) ON DELETE SET NULL,
  FOREIGN KEY (ingredient_id) REFERENCES ingredients(ingredient_id) ON DELETE SET NULL
);
CREATE TABLE recipes (
  recipe_id     INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT NOT NULL,
  recipe_title  VARCHAR(150) NOT NULL,
  description   TEXT,
  instructions  TEXT,
  base_servings INT NOT NULL DEFAULT 1,
  cuisine_type  VARCHAR(50),
  difficulty    ENUM('easy','medium','hard') NOT NULL DEFAULT 'easy',
  prep_time     INT DEFAULT 0,
  cook_time     INT DEFAULT 0,
  image         VARCHAR(255),
  status        ENUM('active','hidden','removed') NOT NULL DEFAULT 'active',
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
CREATE TABLE recipe_ingredients (
  recipe_ingredient_id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id     INT NOT NULL,
  ingredient_id INT NOT NULL,
  quantity      DECIMAL(10,2) NOT NULL DEFAULT 1,
  unit          VARCHAR(30),
  FOREIGN KEY (recipe_id)     REFERENCES recipes(recipe_id) ON DELETE CASCADE,
  FOREIGN KEY (ingredient_id) REFERENCES ingredients(ingredient_id) ON DELETE CASCADE
);
CREATE TABLE saved_recipes (
  saved_id  INT AUTO_INCREMENT PRIMARY KEY,
  user_id   INT NOT NULL,
  recipe_id INT NOT NULL,
  saved_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_recipe (user_id, recipe_id),
  FOREIGN KEY (user_id)   REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id) ON DELETE CASCADE
);
CREATE TABLE carts (
  cart_id    INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
CREATE TABLE cart_items (
  cart_item_id         INT AUTO_INCREMENT PRIMARY KEY,
  cart_id              INT NOT NULL,
  product_id           INT NOT NULL,
  recipe_id            INT NULL,
  recipe_ingredient_id INT NULL,
  quantity             INT NOT NULL DEFAULT 1,
  unit_price           DECIMAL(10,2) NOT NULL,
  added_method         ENUM('manual','recipe') NOT NULL DEFAULT 'manual',
  FOREIGN KEY (cart_id)              REFERENCES carts(cart_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id)           REFERENCES products(product_id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id)            REFERENCES recipes(recipe_id) ON DELETE SET NULL,
  FOREIGN KEY (recipe_ingredient_id) REFERENCES recipe_ingredients(recipe_ingredient_id) ON DELETE SET NULL
);
CREATE TABLE vouchers (
  voucher_id     INT AUTO_INCREMENT PRIMARY KEY,
  store_id       INT NOT NULL,
  voucher_code   VARCHAR(50) NOT NULL,
  discount_type  ENUM('fixed','percentage') NOT NULL,
  discount_value DECIMAL(10,2) NOT NULL,
  minimum_spend  DECIMAL(10,2) DEFAULT 0,
  start_date     DATE,
  end_date       DATE,
  usage_limit    INT DEFAULT 0,
  used_count     INT DEFAULT 0,
  status         ENUM('active','inactive','expired') NOT NULL DEFAULT 'active',
  UNIQUE KEY uniq_store_voucher_code (store_id, voucher_code),
  FOREIGN KEY (store_id) REFERENCES stores(store_id) ON DELETE CASCADE
);
CREATE TABLE orders (
  order_id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id          INT NOT NULL,
  total_amount     DECIMAL(10,2) NOT NULL,
  payment_method   VARCHAR(50),
  payment_status   ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
  order_status     ENUM('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  shipping_address TEXT,
  contact_phone    VARCHAR(20),
  created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
CREATE TABLE merchant_orders (
  merchant_order_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id          INT NOT NULL,
  store_id          INT NOT NULL,
  subtotal          DECIMAL(10,2) NOT NULL,
  voucher_id        INT NULL,
  discount_amount   DECIMAL(10,2) DEFAULT 0,
  delivery_fee      DECIMAL(10,2) DEFAULT 0,
  final_amount      DECIMAL(10,2) NOT NULL DEFAULT 0,
  status            ENUM('pending','accepted','preparing','ready_to_deliver','out_for_delivery','delivered','completed','cancelled') NOT NULL DEFAULT 'pending',
  accepted_at       DATETIME NULL,
  preparing_at      DATETIME NULL,
  ready_to_deliver_at DATETIME NULL,
  out_for_delivery_at DATETIME NULL,
  delivered_at      DATETIME NULL,
  completed_at      DATETIME NULL,
  cancelled_at      DATETIME NULL,
  FOREIGN KEY (order_id)   REFERENCES orders(order_id) ON DELETE CASCADE,
  FOREIGN KEY (store_id)   REFERENCES stores(store_id) ON DELETE CASCADE,
  FOREIGN KEY (voucher_id) REFERENCES vouchers(voucher_id) ON DELETE SET NULL
);
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
CREATE TABLE order_items (
  order_item_id          INT AUTO_INCREMENT PRIMARY KEY,
  merchant_order_id      INT NOT NULL,
  product_id             INT NOT NULL,
  recipe_id              INT NULL,
  recipe_ingredient_id   INT NULL,
  product_name_snapshot  VARCHAR(150) NOT NULL,
  unit_price             DECIMAL(10,2) NOT NULL,
  quantity               INT NOT NULL,
  subtotal               DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (merchant_order_id)      REFERENCES merchant_orders(merchant_order_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id)             REFERENCES products(product_id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id)              REFERENCES recipes(recipe_id) ON DELETE SET NULL,
  FOREIGN KEY (recipe_ingredient_id)   REFERENCES recipe_ingredients(recipe_ingredient_id) ON DELETE SET NULL
);
CREATE TABLE reviews (
  review_id  INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  product_id INT NULL,
  recipe_id  INT NULL,
  rating     INT NOT NULL,
  comment    TEXT,
  status     ENUM('visible','hidden','removed') NOT NULL DEFAULT 'visible',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_product_review (user_id, product_id),
  UNIQUE KEY uniq_user_recipe_review (user_id, recipe_id),
  FOREIGN KEY (user_id)    REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id)  REFERENCES recipes(recipe_id) ON DELETE CASCADE
);
CREATE TABLE reports (
  report_id   INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  target_type ENUM('review','recipe','product') NOT NULL,
  target_id   INT NOT NULL,
  reason      TEXT,
  status      ENUM('pending','reviewed','resolved') NOT NULL DEFAULT 'pending',
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  resolved_at DATETIME NULL,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
