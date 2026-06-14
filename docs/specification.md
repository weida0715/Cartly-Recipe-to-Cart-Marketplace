# SPEC: Implement Cartly Recipe-to-Cart Marketplace Core System

## Overview

Build the **Cartly – Recipe-to-Cart Marketplace** web application based on the existing PHP MVC-style project structure. Cartly is a multi-role food marketplace that connects grocery shopping with recipe-based ingredient purchasing. The system must support Customers, Merchants, and Admins, with the key differentiating feature being a deterministic **Recipe-to-Cart Engine** that converts recipe ingredients into purchasable cart items based on serving size, product availability, package quantity, price, rating, and merchant grouping rules.

This SPEC is based on the current repository structure and the submitted project proposal for **Cartly – Recipe-to-Cart Marketplace**. The proposal defines the platform as a PHP/MySQL web application with customer marketplace features, merchant store management, admin moderation, and a deterministic recipe-to-cart workflow. :contentReference[oaicite:0]{index=0}

---

## Current Repository Context

Current project structure:

```text
.
├── CHANGELOG.md
├── composer.json
├── composer.lock
├── CONTRIBUTING.md
├── docs
│   ├── ai-tools.md
│   ├── architecture.md
│   └── rfc-workflow.md
├── phpunit.xml
├── README.md
├── src
│   ├── app
│   │   ├── controllers
│   │   │   ├── auth
│   │   │   ├── order
│   │   │   ├── product
│   │   │   └── recipe
│   │   ├── helper
│   │   ├── models
│   │   └── views
│   │       ├── auth
│   │       ├── layout
│   │       └── product
│   ├── config
│   ├── database
│   ├── public
│   ├── routes
│   └── uploads
├── tests
│   └── ExampleTest.php
├── tree.txt
└── VERSION
````

The existing project already suggests a PHP MVC direction through `controllers`, `models`, `views`, `routes`, `config`, and `database`. This SPEC expands that structure into the complete Cartly system described in the proposal.

---

## Problem Statement

Traditional food marketplace systems usually separate recipe discovery from ingredient purchasing. Users often find a recipe somewhere else, manually calculate ingredient amounts, search each item one by one, compare products, and then add items to their cart manually.

Cartly solves this by combining:

1. Grocery marketplace browsing.
2. Recipe upload and discovery.
3. Serving-size-based ingredient scaling.
4. Deterministic product matching.
5. Merchant-grouped cart generation.
6. Checkout, order, merchant, and admin workflows.

The system must remain realistic for a PHP, MySQL, HTML, CSS, JavaScript, and XAMPP-based implementation.

---

## Goals

### Primary Goals

* Implement a working multi-role marketplace system.
* Support Customer, Merchant, and Admin role-based access.
* Allow customers to browse products, manage carts, checkout, review products, and manage recipes.
* Allow merchants to manage stores, products, vouchers, and orders.
* Allow admins to manage users, merchants, categories, reports, and platform moderation.
* Implement a deterministic Recipe-to-Cart Engine.
* Store all core system data in MySQL using a normalized relational schema.
* Follow the existing MVC-style structure in `src/app`.

### Non-Goals / Out of Scope

The following are explicitly out of scope for the current implementation:

* Real payment gateway integration.
* Real-time delivery tracking.
* Automated delivery assignment.
* AI-based recommendation.
* External grocery inventory API integration.
* Multi-currency support.
* Native mobile app support.

Payment should be simulated using internal database records only.

---

## User Roles and Access Requirements

### Customer

A Customer can:

* Register an account.
* Login and logout.
* Recover or reset password.
* Edit profile.
* Browse products.
* Search and filter marketplace items.
* View product details.
* Add products manually to cart.
* Upload and manage recipes.
* Search recipes.
* Save recipes.
* Use Recipe-to-Cart generation.
* View cart.
* Apply valid vouchers.
* Checkout with simulated payment.
* View order history.
* Submit product reviews.
* Submit merchant store request.

### Merchant

A Merchant can do everything a Customer can, plus:

* Create a store after approval.
* Manage store profile.
* Add, edit, deactivate, and remove own products.
* Manage product stock.
* Manage vouchers.
* View orders that contain their store products only.
* Update merchant order status.
* View merchant dashboard statistics.
* Deactivate own store.

### Admin

An Admin can:

* Manage user accounts.
* Create or manage admin accounts.
* Approve or reject merchant/store requests.
* Manage product categories.
* Monitor merchant statistics.
* Remove or close stores.
* Review reports.
* Hide, remove, or resolve reported reviews, recipes, and products.
* Enforce account, content, and store moderation.

---

## Functional Requirements

## 1. Authentication and Session Management

### Requirements

* Users must be able to register with:

  * Username
  * Full name
  * Email
  * Phone number
  * Password
* Passwords must be hashed using PHP password hashing.
* Users must be able to login using email or username.
* Users must be able to logout.
* Sessions must track:

  * `user_id`
  * `role`
  * `status`
* Inactive or deactivated users must not be allowed to login.
* Role-based route guards must protect Customer, Merchant, and Admin routes.

### Suggested Files

```text
src/app/controllers/auth/AuthController.php
src/app/models/User.php
src/app/views/auth/login.php
src/app/views/auth/register.php
src/app/views/auth/forgot-password.php
src/app/helper/AuthHelper.php
```

---

## 2. Customer Marketplace Module

### Requirements

* Customers can view active products from approved stores.
* Products can be filtered by category.
* Products can be searched by name or related ingredient.
* Product detail page must show:

  * Product name
  * Description
  * Price
  * Stock quantity
  * Package quantity
  * Package unit
  * Store name
  * Average rating
  * Image
* Customers can add available products to cart manually.
* Out-of-stock or inactive products must not be purchasable.

### Suggested Files

```text
src/app/controllers/product/ProductController.php
src/app/models/Product.php
src/app/models/Category.php
src/app/models/Store.php
src/app/views/product/index.php
src/app/views/product/show.php
```

---

## 3. Recipe Module

### Requirements

Customers must be able to:

* Create recipes.
* Edit own recipes.
* Upload recipe images.
* Add recipe ingredients.
* Set recipe base servings.
* Add instructions.
* Set cuisine type.
* Set difficulty.
* Set preparation and cooking time.
* Search active recipes.
* Save recipes.
* View recipe details.
* Remove or hide own recipes if needed.

Recipe detail page must include:

* Recipe title
* Description
* Image
* Instructions
* Base servings
* Ingredients
* Quantity and unit
* Difficulty
* Cuisine type
* Prep time
* Cook time
* Recipe-to-Cart action

### Suggested Files

```text
src/app/controllers/recipe/RecipeController.php
src/app/models/Recipe.php
src/app/models/RecipeIngredient.php
src/app/models/Ingredient.php
src/app/views/recipe/index.php
src/app/views/recipe/show.php
src/app/views/recipe/create.php
src/app/views/recipe/edit.php
```

---

## 4. Deterministic Recipe-to-Cart Engine

## Core Requirement

The Recipe-to-Cart Engine must convert recipe ingredients into suggested cart items using deterministic, repeatable, explainable rules.

Given:

* Recipe base servings.
* User-selected target servings.
* Recipe ingredients.
* Standard ingredient mappings.
* Product package quantity.
* Product package unit.
* Stock quantity.
* Price.
* Store availability.
* Product/store rating where available.

The system must:

1. Read original recipe serving size.
2. Calculate serving scale factor.
3. Scale required ingredient quantities.
4. Match each ingredient to active products.
5. Calculate required number of packages.
6. Rank products deterministically.
7. Select suitable merchant listings.
8. Group cart preview items by merchant.
9. Show cart preview before confirmation.
10. Add confirmed items into the user’s cart.

### Formula

```text
scale_factor = selected_servings / base_servings

scaled_quantity = recipe_ingredient.quantity * scale_factor

required_packages = CEIL(scaled_quantity / product.package_quantity)
```

### Product Selection Ranking

Products must be ranked by:

1. Fewest required packages.
2. Lowest excess quantity.
3. Lowest product cost.
4. Highest rating.
5. Lowest `product_id` as tie-breaker.

Where:

```text
excess_quantity = (required_packages * package_quantity) - scaled_quantity

product_cost = required_packages * product.price
```

### Merchant Selection Ranking

Merchant/store listing selection must prefer:

1. Product availability and sufficient stock.
2. Merchants already used in the generated cart.
3. Lowest total cost.
4. Highest merchant/store rating.
5. Lowest listing/product ID as tie-breaker.

### Preview Output

The preview page must group generated items by merchant/store:

```text
Store A
- Rice, 2 packages, RM 8.00
- Eggs, 1 package, RM 6.00

Store B
- Chicken Breast, 1 package, RM 12.00
```

### Edge Cases

The engine must handle:

* No matched product for ingredient.
* Product exists but insufficient stock.
* Unit mismatch.
* Product inactive.
* Store not approved.
* Recipe has no ingredients.
* Selected servings less than or equal to zero.
* Duplicate cart items from same recipe generation.

### Suggested Files

```text
src/app/controllers/recipe/RecipeCartController.php
src/app/models/RecipeCartEngine.php
src/app/views/recipe/cart-preview.php
```

---

## 5. Cart Module

### Requirements

* Each customer has one active cart.
* Cart can contain manually added products and recipe-generated products.
* Cart items must store:

  * Product ID
  * Quantity
  * Unit price at time added
  * Added method: `manual` or `recipe`
  * Optional source recipe
  * Optional source recipe ingredient
* Customers can:

  * Update item quantity.
  * Remove item.
  * Clear cart.
  * View cart grouped by merchant.
* Cart totals must be recalculated server-side.

### Suggested Files

```text
src/app/controllers/order/CartController.php
src/app/models/Cart.php
src/app/models/CartItem.php
src/app/views/order/cart.php
```

---

## 6. Checkout and Order Module

### Requirements

* Customer can checkout from cart.
* Checkout must collect or confirm:

  * Shipping address
  * Contact phone
  * Payment method
  * Voucher codes where applicable
* Payment is simulated.
* Order creation must:

  * Create one parent order.
  * Split order into merchant orders by store.
  * Create order items under each merchant order.
  * Snapshot product name and unit price.
  * Deduct product stock.
  * Clear cart after successful order.
* Order status flow:

  * Parent order: `pending`, `processing`, `completed`, `cancelled`
  * Merchant order: `pending`, `accepted`, `preparing`, `completed`, `cancelled`

### Suggested Files

```text
src/app/controllers/order/CheckoutController.php
src/app/controllers/order/OrderController.php
src/app/models/Order.php
src/app/models/MerchantOrder.php
src/app/models/OrderItem.php
src/app/views/order/checkout.php
src/app/views/order/history.php
src/app/views/order/show.php
```

---

## 7. Voucher Module

### Requirements

Merchants can:

* Create vouchers.
* Edit vouchers.
* Deactivate vouchers.
* Set:

  * Voucher code
  * Discount type: fixed or percentage
  * Discount value
  * Minimum spend
  * Start date
  * End date
  * Usage limit

Checkout must validate:

* Voucher belongs to the merchant/store group.
* Voucher is active.
* Current date is within valid date range.
* Minimum spend is satisfied.
* Usage limit is not exceeded.

---

## 8. Merchant Portal

### Requirements

Merchant dashboard must show:

* Total sales.
* Total revenue.
* Order status summary.
* Product stock summary.
* Low-stock products.

Merchant product management must support:

* Create product.
* Edit product.
* Update stock.
* Deactivate product.
* Upload product image.
* Assign category.
* Assign standard ingredient.

Merchant order management must support:

* View merchant-specific orders only.
* Update merchant order status.
* Prevent merchants from viewing other merchants’ orders.

Suggested files:

```text
src/app/controllers/merchant/MerchantDashboardController.php
src/app/controllers/merchant/MerchantProductController.php
src/app/controllers/merchant/MerchantOrderController.php
src/app/controllers/merchant/MerchantVoucherController.php
src/app/models/Store.php
src/app/models/Voucher.php
src/app/views/merchant/dashboard.php
src/app/views/merchant/products.php
src/app/views/merchant/orders.php
src/app/views/merchant/vouchers.php
```

---

## 9. Admin Portal

### Requirements

Admin dashboard must show:

* Total users.
* Total customers.
* Total merchants.
* Pending merchant requests.
* Total stores.
* Total products.
* Total orders.
* Reported content count.

Admin must be able to:

* Approve merchant/store requests.
* Reject merchant/store requests with admin note.
* Deactivate users.
* Manage categories.
* View merchant/store performance.
* Close stores.
* Review reports.
* Hide or remove reported content.

Suggested files:

```text
src/app/controllers/admin/AdminDashboardController.php
src/app/controllers/admin/AdminUserController.php
src/app/controllers/admin/AdminMerchantController.php
src/app/controllers/admin/AdminCategoryController.php
src/app/controllers/admin/AdminReportController.php
src/app/models/Report.php
src/app/views/admin/dashboard.php
src/app/views/admin/users.php
src/app/views/admin/merchant-approval.php
src/app/views/admin/categories.php
src/app/views/admin/reports.php
```

---

# ERD / Database Design

## Tables

The system must implement the following tables:

1. `users`
2. `stores`
3. `categories`
4. `ingredients`
5. `products`
6. `recipes`
7. `recipe_ingredients`
8. `saved_recipes`
9. `carts`
10. `cart_items`
11. `vouchers`
12. `orders`
13. `merchant_orders`
14. `order_items`
15. `reviews`
16. `reports`

---

# Data Dictionary

## `users`

| Field        | Type                                    | Key    | Description                |
| ------------ | --------------------------------------- | ------ | -------------------------- |
| `user_id`    | INT AUTO_INCREMENT                      | PK     | Unique user ID             |
| `username`   | VARCHAR(50)                             | UNIQUE | Username for login/display |
| `full_name`  | VARCHAR(100)                            |        | User full name             |
| `email`      | VARCHAR(100)                            | UNIQUE | Login and recovery email   |
| `phone`      | VARCHAR(20)                             |        | Contact number             |
| `password`   | VARCHAR(255)                            |        | Hashed password            |
| `role`       | ENUM('customer','merchant','admin')     |        | User role                  |
| `status`     | ENUM('active','inactive','deactivated') |        | Account status             |
| `created_at` | DATETIME                                |        | Account creation date      |

## `stores`

| Field               | Type                                           | Key                | Description                   |
| ------------------- | ---------------------------------------------- | ------------------ | ----------------------------- |
| `store_id`          | INT AUTO_INCREMENT                             | PK                 | Unique store ID               |
| `user_id`           | INT                                            | FK → users.user_id | Store owner                   |
| `store_name`        | VARCHAR(100)                                   |                    | Store name                    |
| `store_description` | TEXT                                           |                    | Store details                 |
| `store_logo`        | VARCHAR(255)                                   |                    | Store logo path               |
| `contact_email`     | VARCHAR(100)                                   |                    | Store contact email           |
| `contact_phone`     | VARCHAR(20)                                    |                    | Store contact number          |
| `store_address`     | TEXT                                           |                    | Store address                 |
| `opening_time`      | TIME                                           |                    | Opening time                  |
| `closing_time`      | TIME                                           |                    | Closing time                  |
| `store_status`      | ENUM('pending','approved','rejected','closed') |                    | Store approval/status         |
| `admin_note`        | TEXT                                           |                    | Admin approval/rejection note |
| `created_at`        | DATETIME                                       |                    | Store creation date           |

## `categories`

| Field           | Type                      | Key | Description           |
| --------------- | ------------------------- | --- | --------------------- |
| `category_id`   | INT AUTO_INCREMENT        | PK  | Unique category ID    |
| `category_name` | VARCHAR(100)              |     | Product category name |
| `category_icon` | VARCHAR(255)              |     | Icon/image path       |
| `status`        | ENUM('active','inactive') |     | Category status       |

## `ingredients`

| Field             | Type               | Key    | Description               |
| ----------------- | ------------------ | ------ | ------------------------- |
| `ingredient_id`   | INT AUTO_INCREMENT | PK     | Unique ingredient ID      |
| `ingredient_name` | VARCHAR(100)       | UNIQUE | Standard ingredient name  |
| `base_unit`       | VARCHAR(30)        |        | Standard measurement unit |

## `products`

| Field              | Type                                     | Key                            | Description                 |
| ------------------ | ---------------------------------------- | ------------------------------ | --------------------------- |
| `product_id`       | INT AUTO_INCREMENT                       | PK                             | Unique product ID           |
| `store_id`         | INT                                      | FK → stores.store_id           | Store selling product       |
| `category_id`      | INT                                      | FK → categories.category_id    | Product category            |
| `ingredient_id`    | INT                                      | FK → ingredients.ingredient_id | Standard matched ingredient |
| `product_name`     | VARCHAR(150)                             |                                | Product name                |
| `description`      | TEXT                                     |                                | Product description         |
| `price`            | DECIMAL(10,2)                            |                                | Product price               |
| `stock_quantity`   | INT                                      |                                | Available stock             |
| `package_quantity` | DECIMAL(10,2)                            |                                | Package size                |
| `package_unit`     | VARCHAR(30)                              |                                | Package unit                |
| `image`            | VARCHAR(255)                             |                                | Product image path          |
| `status`           | ENUM('active','inactive','out_of_stock') |                                | Product status              |
| `created_at`       | DATETIME                                 |                                | Product creation date       |

## `recipes`

| Field           | Type                              | Key                | Description           |
| --------------- | --------------------------------- | ------------------ | --------------------- |
| `recipe_id`     | INT AUTO_INCREMENT                | PK                 | Unique recipe ID      |
| `user_id`       | INT                               | FK → users.user_id | Recipe creator        |
| `recipe_title`  | VARCHAR(150)                      |                    | Recipe title          |
| `description`   | TEXT                              |                    | Recipe description    |
| `instructions`  | TEXT                              |                    | Cooking steps         |
| `base_servings` | INT                               |                    | Original serving size |
| `cuisine_type`  | VARCHAR(50)                       |                    | Cuisine type          |
| `difficulty`    | ENUM('easy','medium','hard')      |                    | Difficulty level      |
| `prep_time`     | INT                               |                    | Prep time in minutes  |
| `cook_time`     | INT                               |                    | Cook time in minutes  |
| `image`         | VARCHAR(255)                      |                    | Recipe image path     |
| `status`        | ENUM('active','hidden','removed') |                    | Recipe status         |
| `created_at`    | DATETIME                          |                    | Recipe upload date    |

## `recipe_ingredients`

| Field                  | Type               | Key                            | Description                 |
| ---------------------- | ------------------ | ------------------------------ | --------------------------- |
| `recipe_ingredient_id` | INT AUTO_INCREMENT | PK                             | Unique recipe ingredient ID |
| `recipe_id`            | INT                | FK → recipes.recipe_id         | Related recipe              |
| `ingredient_id`        | INT                | FK → ingredients.ingredient_id | Required ingredient         |
| `quantity`             | DECIMAL(10,2)      |                                | Required quantity           |
| `unit`                 | VARCHAR(30)        |                                | Measurement unit            |

## `saved_recipes`

| Field       | Type               | Key                    | Description                |
| ----------- | ------------------ | ---------------------- | -------------------------- |
| `saved_id`  | INT AUTO_INCREMENT | PK                     | Unique saved recipe record |
| `user_id`   | INT                | FK → users.user_id     | User who saved recipe      |
| `recipe_id` | INT                | FK → recipes.recipe_id | Saved recipe               |
| `saved_at`  | DATETIME           |                        | Saved timestamp            |

## `carts`

| Field        | Type               | Key                | Description        |
| ------------ | ------------------ | ------------------ | ------------------ |
| `cart_id`    | INT AUTO_INCREMENT | PK                 | Unique cart ID     |
| `user_id`    | INT                | FK → users.user_id | Cart owner         |
| `created_at` | DATETIME           |                    | Cart creation time |
| `updated_at` | DATETIME           |                    | Last update time   |

## `cart_items`

| Field                  | Type                    | Key                                          | Description                |
| ---------------------- | ----------------------- | -------------------------------------------- | -------------------------- |
| `cart_item_id`         | INT AUTO_INCREMENT      | PK                                           | Unique cart item ID        |
| `cart_id`              | INT                     | FK → carts.cart_id                           | Related cart               |
| `product_id`           | INT                     | FK → products.product_id                     | Product added              |
| `recipe_id`            | INT NULL                | FK → recipes.recipe_id                       | Source recipe              |
| `recipe_ingredient_id` | INT NULL                | FK → recipe_ingredients.recipe_ingredient_id | Source ingredient          |
| `quantity`             | INT                     |                                              | Number of packages/items   |
| `unit_price`           | DECIMAL(10,2)           |                                              | Price when added           |
| `added_method`         | ENUM('manual','recipe') |                                              | Manual or recipe-generated |

## `vouchers`

| Field            | Type                                | Key                  | Description                   |
| ---------------- | ----------------------------------- | -------------------- | ----------------------------- |
| `voucher_id`     | INT AUTO_INCREMENT                  | PK                   | Unique voucher ID             |
| `store_id`       | INT                                 | FK → stores.store_id | Store offering voucher        |
| `voucher_code`   | VARCHAR(50)                         | UNIQUE with store_id | Store-scoped voucher code     |
| `discount_type`  | ENUM('fixed','percentage')          |                      | Discount type                 |
| `discount_value` | DECIMAL(10,2)                       |                      | Discount amount or percentage |
| `minimum_spend`  | DECIMAL(10,2)                       |                      | Minimum spend                 |
| `start_date`     | DATE                                |                      | Voucher start date            |
| `end_date`       | DATE                                |                      | Voucher end date              |
| `usage_limit`    | INT                                 |                      | Maximum uses                  |
| `used_count`     | INT                                 |                      | Current usage count           |
| `status`         | ENUM('active','inactive','expired') |                      | Voucher status                |

## `orders`

| Field              | Type                                                 | Key                | Description              |
| ------------------ | ---------------------------------------------------- | ------------------ | ------------------------ |
| `order_id`         | INT AUTO_INCREMENT                                   | PK                 | Unique order ID          |
| `user_id`          | INT                                                  | FK → users.user_id | Customer                 |
| `total_amount`     | DECIMAL(10,2)                                        |                    | Final total              |
| `payment_method`   | VARCHAR(50)                                          |                    | Simulated payment method |
| `payment_status`   | ENUM('pending','paid','failed')                      |                    | Payment status           |
| `order_status`     | ENUM('pending','processing','completed','cancelled') |                    | Overall order status     |
| `shipping_address` | TEXT                                                 |                    | Delivery snapshot        |
| `contact_phone`    | VARCHAR(20)                                          |                    | Contact snapshot         |
| `created_at`       | DATETIME                                             |                    | Order date               |

## `merchant_orders`

| Field               | Type                                                           | Key                      | Description              |
| ------------------- | -------------------------------------------------------------- | ------------------------ | ------------------------ |
| `merchant_order_id` | INT AUTO_INCREMENT                                             | PK                       | Unique merchant order ID |
| `order_id`          | INT                                                            | FK → orders.order_id     | Parent order             |
| `store_id`          | INT                                                            | FK → stores.store_id     | Merchant store           |
| `voucher_id`        | INT NULL                                                       | FK → vouchers.voucher_id | Applied voucher          |
| `subtotal`          | DECIMAL(10,2)                                                  |                          | Store subtotal           |
| `discount_amount`   | DECIMAL(10,2)                                                  |                          | Discount amount          |
| `delivery_fee`      | DECIMAL(10,2)                                                  |                          | Delivery fee             |
| `final_amount`      | DECIMAL(10,2)                                                  |                          | Final store amount       |
| `status`            | ENUM('pending','accepted','preparing','completed','cancelled') |                          | Merchant order status    |

## `order_items`

| Field                   | Type               | Key                                          | Description              |
| ----------------------- | ------------------ | -------------------------------------------- | ------------------------ |
| `order_item_id`         | INT AUTO_INCREMENT | PK                                           | Unique order item ID     |
| `merchant_order_id`     | INT                | FK → merchant_orders.merchant_order_id       | Related merchant order   |
| `product_id`            | INT                | FK → products.product_id                     | Purchased product        |
| `recipe_id`             | INT NULL           | FK → recipes.recipe_id                       | Source recipe            |
| `recipe_ingredient_id`  | INT NULL           | FK → recipe_ingredients.recipe_ingredient_id | Source ingredient        |
| `product_name_snapshot` | VARCHAR(150)       |                                              | Product name at purchase |
| `quantity`              | INT                |                                              | Purchased quantity       |
| `unit_price`            | DECIMAL(10,2)      |                                              | Unit price at purchase   |
| `subtotal`              | DECIMAL(10,2)      |                                              | Quantity × unit price    |

## `reviews`

| Field        | Type                               | Key                      | Description              |
| ------------ | ---------------------------------- | ------------------------ | ------------------------ |
| `review_id`  | INT AUTO_INCREMENT                 | PK                       | Unique review ID         |
| `user_id`    | INT                                | FK → users.user_id       | Reviewer                 |
| `product_id` | INT NULL                           | FK → products.product_id | Reviewed product         |
| `recipe_id`  | INT NULL                           | FK → recipes.recipe_id   | Reviewed recipe          |
| `rating`     | INT                                |                          | Rating value             |
| `comment`    | TEXT                               |                          | Review comment           |
| `status`     | ENUM('visible','hidden','removed') |                          | Review moderation status |
| `created_at` | DATETIME                           |                          | Review date              |

## `reports`

| Field         | Type                                  | Key                | Description            |
| ------------- | ------------------------------------- | ------------------ | ---------------------- |
| `report_id`   | INT AUTO_INCREMENT                    | PK                 | Unique report ID       |
| `user_id`     | INT                                   | FK → users.user_id | Reporting user         |
| `target_type` | ENUM('review','recipe','product')     |                    | Reported content type  |
| `target_id`   | INT                                   |                    | ID of reported content |
| `reason`      | TEXT                                  |                    | Report reason          |
| `status`      | ENUM('pending','reviewed','resolved') |                    | Report status          |
| `created_at`  | DATETIME                              |                    | Report creation date   |

---

# Design Requirements

## Architecture Pattern

Use MVC:

```text
Model      = database access and business rules
View       = page templates and UI
Controller = request handling and response routing
```

## Proposed Expanded Structure

```text
src
├── app
│   ├── controllers
│   │   ├── admin
│   │   ├── auth
│   │   ├── customer
│   │   ├── merchant
│   │   ├── order
│   │   ├── product
│   │   └── recipe
│   ├── helper
│   │   ├── AuthHelper.php
│   │   ├── FileUploadHelper.php
│   │   ├── ValidationHelper.php
│   │   └── ViewHelper.php
│   ├── models
│   │   ├── User.php
│   │   ├── Store.php
│   │   ├── Category.php
│   │   ├── Ingredient.php
│   │   ├── Product.php
│   │   ├── Recipe.php
│   │   ├── RecipeIngredient.php
│   │   ├── RecipeCartEngine.php
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Voucher.php
│   │   ├── Order.php
│   │   ├── MerchantOrder.php
│   │   ├── OrderItem.php
│   │   ├── Review.php
│   │   └── Report.php
│   └── views
│       ├── admin
│       ├── auth
│       ├── customer
│       ├── layout
│       ├── merchant
│       ├── order
│       ├── product
│       └── recipe
├── config
├── database
│   ├── migrations
│   └── seeders
├── public
├── routes
└── uploads
```

---

# Security Requirements

* Use prepared statements for all database queries.
* Hash all passwords.
* Never store plain-text passwords.
* Validate all server-side inputs.
* Escape all output in views.
* Protect role-specific routes.
* Prevent customers from accessing merchant/admin pages.
* Prevent merchants from editing other merchants’ products/orders.
* Validate file uploads:

  * Allow image MIME types only.
  * Limit file size.
  * Generate safe filenames.
  * Store outside executable paths when possible.
* Protect against CSRF for forms if project helper support exists.
* Do not trust client-side totals during checkout.
* Recalculate cart, voucher, and order totals server-side.
* Keep historical order snapshots.

---

# Implementation Plan

## Phase 1: Database and Base Infrastructure

* Create migration SQL for all required tables.
* Add seed data for:

  * Admin user
  * Categories
  * Ingredients
  * Sample merchants
  * Sample products
  * Sample recipes
* Configure database connection.
* Add base model class or database helper.
* Add shared layout templates.
* Add routing conventions.

Deliverables:

* `src/database/migrations/*.sql`
* `src/database/seeders/*.sql`
* `src/config/database.php`
* Base model/database helper.

---

## Phase 2: Authentication and RBAC

* Implement registration.
* Implement login.
* Implement logout.
* Implement session guard.
* Implement role guard.
* Implement account status validation.
* Add default admin account seed.

Deliverables:

* Auth controller.
* User model.
* Auth views.
* Auth helper.

---

## Phase 3: Customer Marketplace

* Implement category listing.
* Implement product listing.
* Implement product search.
* Implement product detail.
* Implement manual add-to-cart.

Deliverables:

* Product controller.
* Product model.
* Product listing/detail views.
* Cart insertion logic.

---

## Phase 4: Cart Management

* Implement active cart creation.
* Implement cart item listing.
* Implement update quantity.
* Implement remove item.
* Implement clear cart.
* Group cart items by merchant.

Deliverables:

* Cart controller.
* Cart and CartItem models.
* Cart view.

---

## Phase 5: Recipe Management

* Implement recipe CRUD.
* Implement recipe image upload.
* Implement ingredient selection.
* Implement recipe search.
* Implement saved recipes.
* Implement recipe detail page.

Deliverables:

* Recipe controller.
* Recipe model.
* RecipeIngredient model.
* SavedRecipe model.
* Recipe views.

---

## Phase 6: Recipe-to-Cart Engine

* Implement serving scale calculation.
* Implement ingredient quantity scaling.
* Implement product matching by ingredient.
* Implement package calculation.
* Implement deterministic ranking.
* Implement merchant grouping.
* Implement preview page.
* Implement confirm-add-to-cart action.
* Add tests for deterministic ranking behavior.

Deliverables:

* `RecipeCartEngine.php`
* `RecipeCartController.php`
* Cart preview view.
* Unit tests.

---

## Phase 7: Checkout and Orders

* Implement checkout page.
* Implement voucher validation.
* Implement simulated payment.
* Implement order creation transaction.
* Implement merchant order splitting.
* Implement order item snapshots.
* Deduct stock.
* Clear cart after successful checkout.
* Implement order history.

Deliverables:

* Checkout controller.
* Order models.
* Order views.
* Voucher validation logic.

---

## Phase 8: Merchant Portal

* Implement merchant store request.
* Implement store profile management.
* Implement product CRUD.
* Implement voucher CRUD.
* Implement merchant order list.
* Implement merchant order status update.
* Implement merchant dashboard statistics.

Deliverables:

* Merchant controllers.
* Merchant views.
* Store/Product/Voucher logic.

---

## Phase 9: Admin Portal

* Implement admin dashboard.
* Implement user management.
* Implement merchant approval/rejection.
* Implement category management.
* Implement report moderation.
* Implement store closure.

Deliverables:

* Admin controllers.
* Admin views.
* Admin models/actions.

---

## Phase 10: Testing and Documentation

* Add PHPUnit tests for:

  * Auth validation.
  * Role guards.
  * Recipe-to-Cart calculations.
  * Voucher validation.
  * Checkout order splitting.
* Update README.
* Update architecture documentation.
* Add setup instructions.
* Add seed data instructions.

Deliverables:

* PHPUnit tests.
* Updated docs.
* Updated README.

---

# Acceptance Criteria

## Authentication

* [ ] User can register successfully.
* [ ] User password is hashed.
* [ ] User can login and logout.
* [ ] Deactivated users cannot login.
* [ ] Unauthorized users are redirected away from protected routes.
* [ ] Role guards correctly separate Customer, Merchant, and Admin pages.

## Marketplace

* [ ] Products from approved stores are visible.
* [ ] Products from closed or pending stores are hidden.
* [ ] Inactive/out-of-stock products cannot be purchased.
* [ ] Product search works by product name.
* [ ] Category filtering works.

## Recipe

* [ ] Customer can create recipe.
* [ ] Recipe supports ingredients, servings, instructions, and image.
* [ ] Customer can search recipes.
* [ ] Customer can save recipes.
* [ ] Recipe detail shows ingredients and Recipe-to-Cart action.

## Recipe-to-Cart

* [ ] User can select target servings.
* [ ] System calculates scaled ingredient quantities.
* [ ] System calculates required packages.
* [ ] Product ranking is deterministic.
* [ ] Merchant grouping is deterministic.
* [ ] Unmatched ingredients are shown clearly.
* [ ] User sees preview before cart insertion.
* [ ] Confirming preview adds items to cart.
* [ ] Same input data always produces same output.

## Cart and Checkout

* [ ] Cart supports manual and recipe-generated items.
* [ ] Cart items can be updated and removed.
* [ ] Checkout creates parent order.
* [ ] Checkout splits merchant orders by store.
* [ ] Order items store product snapshots.
* [ ] Stock is deducted after checkout.
* [ ] Cart is cleared after checkout.
* [ ] Order history shows past orders.

## Merchant

* [ ] Merchant can manage own products only.
* [ ] Merchant can create and manage vouchers.
* [ ] Merchant can view only own merchant orders.
* [ ] Merchant can update order status.
* [ ] Merchant dashboard displays summary statistics.

## Admin

* [ ] Admin can approve/reject merchant requests.
* [ ] Admin can manage users.
* [ ] Admin can manage categories.
* [ ] Admin can review reports.
* [ ] Admin can hide/remove reported content.
* [ ] Admin can close stores.

## Security

* [ ] SQL queries use prepared statements.
* [ ] Output is escaped.
* [ ] File uploads are validated.
* [ ] Checkout totals are recalculated server-side.
* [ ] Merchant data ownership is enforced.
* [ ] Admin-only actions are protected.

---

# Testing Notes

Minimum PHPUnit test coverage should include:

```text
tests/AuthTest.php
tests/RoleGuardTest.php
tests/RecipeCartEngineTest.php
tests/VoucherTest.php
tests/CheckoutTest.php
```

Important Recipe-to-Cart test cases:

1. 2-serving recipe scaled to 4 servings.
2. Product with fewer required packages wins.
3. Product with lower excess quantity wins.
4. Product with lower total cost wins.
5. Higher rating wins when cost ties.
6. Lower product ID wins as final tie-breaker.
7. Insufficient stock products are excluded.
8. Unmatched ingredient appears in preview as unavailable.
9. Same input returns same selected products.
