# Changelog

All notable changes to this project will be documented in this file.

## [0.1.0] - 3rd June 2026

- Create initial repository directory structure
- Add project overview and getting started guide
- Add repository gitignore configuration
- Configure PHP code quality and basic frontend formatting rules
- Add environment variable template
- Establish application folder conventions
- Create initial testing framework structure
- Document project structure and development conventions
- Add changelog and version tracking system

## [0.1.1] - 3rd June 2026

- Create pull request template with required review checklist

## [0.1.2] - 3rd June 2026

- Create CONTRIBUTING.md
- Define branching strategy (e.g. main, develop, feature branches)
- Define pull request requirements (review rules, approvals, etc.)
- Document commit message conventions (e.g. Conventional Commits)
- Add coding standards (naming, structure, formatting rules)
- Define issue templates (bug, feature, improvement)

## [1.0.0] - 11th June 2026
- Front controller, router, autoloader, base Controller/Model, AuthHelper, CSRF, Flash, Validator.
- Full 16-table MySQL schema + seed (`src/database/`).
- Auth (login, register, forgot password, logout) with role-aware redirects.
- Customer marketplace: product index, detail, search/filter, cart with stepper.
- Recipe module: create, edit, show, save.
- Deterministic Recipe-to-Cart engine with merchant grouping and edge-case warnings.
- Checkout, parent + merchant orders, simulated payment, stock deduction, voucher application.
- Merchant portal: dashboard, products, orders, vouchers, store profile.
- Admin portal: dashboard, user/merchant/category/report management.
- PHPUnit tests for the Recipe-to-Cart engine.

## [1.0.1] - 13th June 2026
- Deleted `scripts/preview-stub.js`
- Deleted `package.json` because it only contained the preview stub `dev` script
- Removed Lovable/preview-specific text from `README.md`
- Kept `archive/lovable/` and all other archive assets intact

## [1.0.2] - 14th June 2026
- Added server-side validation for merchant voucher creation inputs.
- Added voucher date-range validation and duplicate-code handling through the store-scoped database constraint.
- Changed voucher codes to be unique per store and added migration `002_store_scoped_voucher_codes.sql`.
- Ignored local proposal files with `Proposal/` in `.gitignore`.

## [1.0.3] - 14th June 2026
- Added a homepage promotional banner that highlights a recipe-to-cart offer.
- Added responsive custom CSS for the promotional campaign section.

## [1.0.4] - 15th June 2026
- Added a grouped footer across shared customer, merchant, admin, auth, and error page layouts.
- Added responsive custom CSS for footer link columns and footer metadata.

## [1.0.5] - 15th June 2026
- Improved the forgot-password flow so local/test environments can display a usable reset link without exposing it in production.
- Added backend email validation and clearer reset-password form autocomplete/labels.

## [1.0.6] - 15th June 2026
- Fixed marketplace product search by using distinct prepared-statement placeholders for each searchable column.
- Improved the marketplace search input so clearing it resets filtered results immediately.

## [1.0.7] - 15th June 2026
- Fixed recipe search by using distinct prepared-statement placeholders for recipe title and description.
- Kept the recipe search box scoped to recipe text, kept cuisine as a separate filter, and reset results when either clear button is used.

## [1.0.8] - 15th June 2026
- Changed the recipe cuisine filter to support partial cuisine matches.
- Updated the recipe cuisine filter placeholder so it is clear that the field filters cuisine.

## [1.0.9] - 15th June 2026
- Added saved-state detection to recipe detail pages.
- Updated the recipe save button to show Save recipe or Unsave recipe based on the current user's saved state.

## [1.0.10] - 16th June 2026
- Reworked the homepage with a stronger split hero, recipe-to-cart preview panel, section headings, category tiles, and clearer product/recipe card fallbacks.
- Improved the shared Cartly visual system with stronger navigation treatment, clearer focus states, richer shadows, polished buttons, cleaner forms, and refined tables.
- Improved responsive styling for category tiles, filters, marketplace cards, dashboard sidebars, and auth pages using custom CSS only.
- Fixed the PHP deprecation warning in `Validator::required()` by explicitly marking the optional label parameter as nullable.
- Added reusable phone validation logic to reject non-numeric phone input.
- Applied digit-only phone validation to registration, customer profile, merchant request, merchant store profile, and checkout forms.
- Added numeric-friendly phone input attributes and PHPUnit coverage for the validator rule.
- Hardened homepage recipe metadata output and required-field validation against nullable values and array inputs.
