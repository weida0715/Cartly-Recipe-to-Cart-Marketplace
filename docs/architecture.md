# Cartly – Architecture & Development Conventions

This document defines the project structure, architecture decisions, and development conventions for the Cartly platform.

---

# 1. Architecture Overview

Cartly follows a **custom MVC (Model–View–Controller) architecture** using PHP without a heavy framework.

The system is designed to:

- Separate business logic from presentation
- Keep code modular and maintainable
- Support role-based features (Customer, Merchant, Admin)
- Enable scalable feature development (e.g. Recipe-to-Cart Engine)

---

# 2. Folder Structure

## Root Structure

```text id="a2"
src/
├── public/          # Entry point (index.php, exposed assets)
├── app/             # Core application logic
├── config/          # Configuration files (DB, app settings)
├── database/        # SQL schema, migrations, and seeders
├── uploads/         # User-generated files (images, media)
├── tests/           # PHPUnit test cases
├── docs/            # Project documentation
├── vendor/          # Composer dependencies (ignored in git)
└── phpunit.xml      # Test configuration
````

---

## Application Layer (`app/`)

```text id="a3"
app/
├── controllers/     # Handles HTTP requests and responses
├── models/          # Database entities and business logic
├── views/           # UI templates (HTML/PHP views)
├── helpers/         # Reusable utility functions
└── helpers/         # Reusable utility functions
```

Current helper set includes `AuthHelper`, `Csrf`, `Flash`, `Validator`,
`FileUploadHelper`, `Controller`, `Model`, and `Router`.

---

## Configuration (`config/`)

Contains all environment and system configuration:

* database connection settings
* application constants
* environment-based settings (dev/prod)

Example:

```text id="a4"
config/
├── database.php
├── app.php
```

---

## Public Directory (`public/`)

This is the **only web-accessible entry point**.

Contains:

* `index.php` (front controller)
* static assets (if needed)
* routing entry logic (if implemented)

---

## Uploads (`uploads/`)

Stores runtime user-generated files:

* product images
* recipe images
* customer/merchant-uploaded images

Structure:

```text id="a5"
uploads/
├── products/
├── recipes/
├── users/
```

Files in this directory are NOT tracked by Git.

---

## Tests (`tests/`)

Uses PHPUnit for testing core logic.

Current coverage includes deterministic Recipe-to-Cart rules in
`tests/RecipeCartEngineTest.php`. Additional auth, voucher, checkout, and role
guard tests are still pending to fully match the spec.

Structure:

```text id="a6"
tests/
├── Unit/
├── Feature/
└── ExampleTest.php
```

---

# 3. Architecture Rules

## MVC Separation Rule

* Controllers handle requests only
* Models handle data and business logic
* Views handle presentation only

❌ Do NOT put SQL queries inside controllers
❌ Do NOT put HTML inside models
❌ Do NOT mix logic inside views

---

## Business Logic Rule

All core logic must be placed in:

* Models OR
* Helper classes

Example:

✔ Recipe-to-Cart Engine logic belongs in:

```text id="a7"
app/models/Recipe.php
app/helpers/CartHelper.php
```

---

# 4. Naming Conventions

## Files

* PascalCase for classes

  * `UserController.php`
  * `ProductModel.php`

* snake_case for helpers

  * `cart_helper.php`

---

## Commits

Follow Conventional Commits:

```text id="a8"
feat: new feature
fix: bug fix
docs: documentation
chore: maintenance
refactor: code restructuring
test: testing updates
ci: CI/CD changes
```

Example:

```text id="a9"
feat(recipe): implement ingredient scaling logic
```

---

## Branch Naming

```text id="a10"
feature/issue-12-login-page
fix/issue-18-cart-bug
docs/issue-21-readme-update
refactor/issue-30-model-cleanup
test/issue-35-recipe-engine
```

---

# 5. Development Workflow

This project follows an **issue-first workflow**:

```text id="a11"
Issue → Discussion → Branch → Development → Commit → PR → AI Review → Admin Review → Merge
```

---

## Branch Rules

* No direct commits to `main`
* Every branch must reference an issue
* All PRs must pass CI checks
* All PRs must be reviewed before merging

---

# 6. Testing Convention

* Unit tests for models and helpers
* Feature tests for workflows (auth, cart, recipe engine)
* Use PHPUnit

Run tests:

```bash id="a12"
vendor/bin/phpunit
```

---

# 7. CI/CD Expectations

Every pull request must:

* Pass PHP lint checks
* Pass PHPUnit tests (when available)
* Contain no unrelated changes
* Be linked to an issue

---

# 8. Security Rules

* Never commit `.env` files
* Never expose database credentials
* Validate all user inputs server-side
* Sanitize all outputs in views
* Use prepared statements for SQL

---

# 9. Key Design Decision: Recipe-to-Cart Engine

The Recipe-to-Cart Engine is the core system feature.

It must:

* Be deterministic (same input → same output)
* Be independent of UI layer
* Be testable via PHPUnit

### Related implementation notes

- The engine ranks products by package count, excess quantity, cost, rating,
  and product ID as a tie-breaker.
- Merchant/store grouping prefers merchants already used in the preview.
- Checkout persists order snapshots so later product changes do not alter past
  orders.

* Not depend on controllers or views

---

# 10. Contributor Expectations

All contributors must:

* Follow MVC structure strictly
* Write meaningful commit messages
* Link PRs to issues
* Ensure code is testable and readable
* Avoid mixing logic layers
* Run tests before submitting PR

---

# 11. Final Note

This architecture prioritizes:

* Simplicity (no framework dependency)
* Maintainability
* Clear separation of concerns
* Testability
* Deterministic business logic
