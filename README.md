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

## Project setup

Cartly is a plain PHP MVC application backed by MySQL/MariaDB. You can run it
with XAMPP/MAMP/WAMP or with PHP's built-in development server. Linux users can
run the app directly with the `php` command.

### 1. Install system requirements

Install these tools before setting up the project:

- PHP 8.1+ with the `pdo_mysql`, `mbstring`, `fileinfo`, and `session` extensions
- MySQL 5.7+ or MariaDB 10.4+
- Composer
- Git

#### Windows

Recommended beginner setup:

1. Install [XAMPP](https://www.apachefriends.org/) for Apache, PHP, and MySQL.
2. Install [Composer](https://getcomposer.org/download/).
3. Install [Git for Windows](https://git-scm.com/download/win).

If you use XAMPP, ensure PHP is available in your terminal by adding the XAMPP
PHP folder, for example `C:\xampp\php`, to your `PATH`.

#### macOS

Using Homebrew:

```bash
brew install php composer mysql git
brew services start mysql
```

Alternatively, install MAMP and Composer separately if you prefer a bundled GUI
environment.

#### Linux

On Ubuntu/Debian-based systems:

```bash
sudo apt update
sudo apt install php php-cli php-mysql php-mbstring php-xml php-curl php-zip php-gd mysql-server composer git
sudo systemctl enable --now mysql
```

On Fedora-based systems:

```bash
sudo dnf install php php-cli php-pdo php-mysqlnd php-mbstring php-xml php-curl php-zip php-gd mysql-server composer git
sudo systemctl enable --now mysqld
```

### 2. Clone the project

```bash
git clone https://github.com/weida0715/Cartly-Recipe-to-Cart-Marketplace.git
cd Cartly-Recipe-to-Cart-Marketplace
```

For XAMPP/WAMP/MAMP, you may instead place the project folder inside your web
root, for example `xampp/htdocs/cartly` on Windows.

### 3. Install PHP dependencies

```bash
composer install
```

Composer installs development dependencies such as PHPUnit into `vendor/`.

### 4. Create and seed the database

Create a database named `cartly`, then import the schema and seed data.

Using the MySQL CLI:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS cartly CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p cartly < src/database/schema.sql
mysql -u root -p cartly < src/database/seed.sql
php src/database/seed_assets.php
```

If your local MySQL root user has no password, omit `-p`:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS cartly CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root cartly < src/database/schema.sql
mysql -u root cartly < src/database/seed.sql
php src/database/seed_assets.php
```

You can also import the same files with phpMyAdmin:

1. Create a database named `cartly`.
2. Import `src/database/schema.sql`.
3. Import `src/database/seed.sql`.
4. From the project root, run `php src/database/seed_assets.php` so seeded
   product and recipe images are copied into `src/public/uploads/seeded/`.

Optional spec-layout copies are also available at:

- `src/database/migrations/001_cartly_schema.sql`
- `src/database/seeders/001_cartly_seed.sql`

### 5. Configure database credentials

Edit `src/config/database.php` to match your local MySQL/MariaDB user:

```php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'cartly');
define('DB_USER', 'cartly_user');
define('DB_PASS', 'cartly_password');
```

For a default XAMPP install, this is often:

```php
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 6. Run the application

#### Option A: PHP built-in server — works on Windows, macOS, and Linux

From the project root, run:

```bash
php -S localhost:8000 -t src/public
```

Then open <http://localhost:8000>.

#### Option B: XAMPP/WAMP/MAMP

1. Put the project in your web root, for example `xampp/htdocs/cartly`.
2. Start Apache and MySQL.
3. Open <http://localhost/cartly/src/public/>.

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
