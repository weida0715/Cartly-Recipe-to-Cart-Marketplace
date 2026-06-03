# Cartly

Cartly is a recipe-driven grocery marketplace that allows users to convert recipes directly into purchasable shopping carts.

Unlike traditional grocery platforms where users manually search for ingredients, Cartly provides a deterministic Recipe-to-Cart Engine that:

* Scales ingredient quantities based on serving size
* Matches ingredients to available products
* Selects optimal merchant listings
* Generates a ready-to-checkout shopping cart

## Key Features

### Customer

* Browse products and recipes
* Generate carts from recipes
* Manage shopping carts and orders
* Review products
* Save favorite recipes

### Merchant

* Manage stores and inventory
* Process customer orders
* Create vouchers
* Monitor store performance

### Administrator

* Approve merchants
* Manage categories and users
* Moderate content
* Monitor platform activity

## Core Innovation

### Recipe-to-Cart Engine

1. Select a recipe
2. Adjust serving size
3. Scale ingredient quantities
4. Match ingredients to products
5. Select merchant listings
6. Generate a cart preview

The engine uses deterministic ranking rules to ensure identical inputs always produce identical cart outputs.

## Technology Stack

* Frontend: HTML, CSS, JavaScript
* Backend: PHP
* Database: MySQL
* Architecture: MVC
* Development Environment: XAMPP

## Project Structure

```text
src/
├── public/
├── admin/
├── merchant/
├── app/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   └── helpers/
├── assets/
├── config/
├── database/
└── uploads/
```

## Getting Started

### Prerequisites

* PHP 8+
* MySQL
* XAMPP

### Installation

```bash
git clone <repository-url>
cd cartly
```

Configure:

```text
config/database.php
```

Import:

```text
database/cartly.sql
```

Start Apache and MySQL using XAMPP.

## Documentation

Detailed project documentation can be found in:

```text
docs/
├── architecture.md
├── database-design.md
├── recipe-to-cart-engine.md
├── security.md
└── roadmap.md
```

## License

Developed for educational purposes as part of CIT6224 Web Application Development.
