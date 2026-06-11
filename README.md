# Cartly — Recipe-to-Cart Marketplace (PHP MVC)

A multi-role food marketplace built in PHP MVC for XAMPP / MySQL. Customers
browse a grocery marketplace, upload recipes, and use a deterministic
**Recipe-to-Cart engine** to turn ingredients into a merchant-grouped cart.
Merchants manage products, orders, and vouchers. Admins moderate users,
stores, categories, and reports.

## Stack
- PHP 8.1+ (no framework, custom mini-MVC under `src/app/`)
- MySQL 5.7+ / MariaDB 10.4+
- Plain HTML + CSS + vanilla JS
- PHPUnit for tests

The `archive/` folder contains the original React/shadcn design prototype.
It is reference-only and is NOT part of the runtime project.

## Setup (XAMPP)

1. Copy this project into `xampp/htdocs/cartly`.
2. Start Apache + MySQL.
3. In phpMyAdmin, import:
   - `src/database/schema.sql`
   - `src/database/seed.sql`
4. Optional spec layout copies are also available at:
   - `src/database/migrations/001_cartly_schema.sql`
   - `src/database/seeders/001_cartly_seed.sql`
5. Visit <http://localhost/cartly/src/public/>.

DB credentials default to `root` with no password — change in `src/config/database.php`.

## Default logins

| Role     | Username  | Password    |
|----------|-----------|-------------|
| Admin    | admin     | password123 |
| Merchant | merchant  | password123 |
| Customer | customer  | password123 |

(See `src/database/README.md` if you need to regenerate the hash.)

## Implemented features

- Customer marketplace browsing, category filtering, and product details
- Recipe CRUD, recipe images, saved recipes, and recipe-to-cart preview
- Manual cart, recipe-generated cart items, voucher validation, and checkout
- Merchant product, voucher, order, and store management
- Admin user, merchant approval, category, report, and store moderation
- Customer profile editing and password reset flow

## Project layout

```
src/
├── app/
│   ├── controllers/{auth,product,recipe,order,customer,merchant,admin}/
│   ├── helper/        Router, Controller, Model, AuthHelper, Csrf, Validator, Flash
│   ├── models/        16 models matching the schema
│   └── views/         Plain PHP templates + layout partials
├── config/            config.php, database.php
├── database/          schema.sql, seed.sql
├── public/            index.php (front controller), .htaccess, assets/
├── routes/web.php     Route table
├── public/uploads/    Publicly served user-uploaded images
└── uploads/           Legacy upload directory, not used for new uploads
tests/                 PHPUnit suite (RecipeCartEngineTest)
archive/               Original React/shadcn design prototype (reference)
```

## Recipe-to-Cart engine

Implements the deterministic algorithm from the spec:

```
scale_factor      = selected_servings / base_servings
scaled_quantity   = recipe_ingredient.quantity * scale_factor
required_packages = CEIL(scaled_quantity / product.package_quantity)
```

Products are ranked by: merchants already used → fewest packages → lowest
excess → lowest cost → highest rating → lowest product_id. See
`src/app/models/RecipeCartEngine.php`. Edge cases (no match, unit mismatch,
insufficient stock, inactive product, no ingredients, servings ≤ 0) are
surfaced as warnings on the preview screen.

## Running tests

```bash
composer install
vendor/bin/phpunit
```

Current automated coverage focuses on `tests/RecipeCartEngineTest.php`.

## Lovable preview

The Lovable in-browser preview cannot execute PHP. A placeholder page is
served on the preview port via `scripts/preview-stub.js` so the dev harness
stays healthy. Run the project locally under XAMPP to use it.
