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
- Added reusable phone validation logic to reject non-numeric phone input.
- Applied digit-only phone validation to registration, customer profile, merchant request, merchant store profile, and checkout forms.
- Added numeric-friendly phone input attributes and PHPUnit coverage for the validator rule.

## [1.1.0] - 16th June 2026
- Reworked the homepage with a stronger split hero, recipe-to-cart preview panel, section headings, category tiles, and clearer product/recipe card fallbacks.
- Improved the shared Cartly visual system with stronger navigation treatment, clearer focus states, richer shadows, polished buttons, cleaner forms, and refined tables.
- Improved responsive styling for category tiles, filters, marketplace cards, dashboard sidebars, and auth pages using custom CSS only.
- Fixed the PHP deprecation warning in `Validator::required()` by explicitly marking the optional label parameter as nullable.
- Hardened homepage recipe metadata output and required-field validation against nullable values and array inputs.

## [1.1.1] - 16th June 2026
- Added versioned stock seed images for categories, seeded ingredient/product thumbnails, and seeded recipes.
- Added `src/database/seed_assets.php` to copy stock image sources into `src/public/uploads/seeded/` for repeatable XAMPP reseeding.
- Updated seed SQL files so category, product, and recipe records reference seeded image paths.
- Updated recipe listing cards to render seeded recipe images instead of the fallback placeholder.
- Documented the image generation prompts and the extra seed asset copy step.
- Added reusable phone validation logic to reject non-numeric phone input.
- Applied digit-only phone validation to registration, customer profile, merchant request, merchant store profile, and checkout forms.
- Added numeric-friendly phone input attributes and PHPUnit coverage for the validator rule.

## [1.1.2] - 17th June 2026
- Hid admin accounts from the admin user-management list.
- Blocked direct admin user-management requests from changing admin account roles or statuses.

## [1.1.3] - 17th June 2026
- Added report status totals to the content moderation page.
- Added target details for reported products, recipes, and reviews.

## [1.1.4] - 17th June 2026
- Allows product and recipe reviews to be submitted without a comment.
- Limits each customer to one review per product or recipe.
- Changes repeat review submissions into edits of the customer’s existing review.
- Pre-fills the review form when the customer has already reviewed the product or recipe.
- Updates the review form heading/button to show edit/update behavior.
- Adds database uniqueness constraints for one review per customer per product/recipe.
- Adds migration `003_unique_customer_reviews.sql`.

## [1.1.5] - 18th June 2026
- Added server-side validation for merchant product payload fields to reject empty names, empty or non-numeric numeric fields, negative prices, negative stock quantities, and non-positive package quantities.
- Added inline merchant product form validation messages for invalid manual input without using browser popup dialogs.
- Kept product price and stock number controls bounded to a minimum value of zero.

## [1.1.6] - 19th June 2026
- Fixed customer order history labels by starting new parent orders as pending and synchronizing parent order status from merchant order updates.
- Added merchant order status validation so customer, merchant, and order detail views use consistent order states.
- Updated customer order history, dashboard, confirmation, and detail pages to display status labels derived from merchant order statuses.
- Treated mixed completed/cancelled merchant orders as completed and made merchant order status synchronization transactional.

## [1.1.7] - 19th June 2026
- Added active navigation highlighting to the shared customer navigation.
- Kept parent navigation items active on product, recipe, order, merchant, and admin child pages.
- Added accessible `aria-current="page"` attributes and distinct merchant/admin sidebar active states.

## [1.1.8] - 19th June 2026
- Added a linked cart icon and item-count badge to the Marketplace page.
- Calculated the cart indicator count on the server for the logged-in customer.

## [1.1.9] - 19th June 2026
- Updated the marketplace voucher banner copy to promote browsing available merchant vouchers.
- Added a public available vouchers page with merchant/code search, discount type filtering, and voucher sorting.
- Linked checkout voucher entry to store-scoped available voucher code suggestions.
- Restricted checkout voucher suggestions and redemption to vouchers valid for each merchant and subtotal.
- Improved checkout voucher error handling by returning invalid store voucher codes back to checkout with merchant-specific feedback.

## [1.1.10] - 19th June 2026
- Added immediate cart update validation to reject item quantities that exceed available product stock.
- Added cart quantity maximum hints and stock labels to the cart update form.

## [1.1.11] - 19th June 2026
- Added D3 chart visualizations to admin dashboard, merchant dashboard, and admin report statistics pages.
- Added merchant dashboard revenue, orders, products, average order value, and revenue change metric cards.
- Added weekly merchant sales bar chart and order trend line chart with Mon-Sun x-axis labels and hover value tooltips.
- Added recent merchant order item counts to the dashboard order summary.

## [1.1.12] - 19th June 2026
- Added admin category editing from the Category Management page.
- Added per-category Edit and Deactivate actions in the same category card action row.
- Added category update route and controller handling with CSRF and required-name validation.
- Improved D3 loading so chart pages opt in instead of loading D3 on every page.
- Hardened merchant dashboard date parsing and cart item lookup handling from code review feedback.

## [1.2.0] - 20th June 2026
- Added live delivery tracking refresh on both customer order pages and the merchant orders page so the progress line and nodes stay in sync.
- Added merchant-side tracking polling plus store-scoped tracking status endpoints so order progression updates are visible from both roles.
- Fixed the order controller load failure caused by a missing PHP opening tag in `OrderController.php`.
- Hardened customer order status updates so database failures now surface error flashes instead of false success messages.
- Excluded cancelled orders from merchant dashboard revenue, order count, and average order value metrics.
- Added a safe fallback for checkout voucher store labels to avoid undefined array key warnings.

## [1.2.1] - 20th June 2026
- Store listing page for all users.
- Store detail page showing merchant products and store-specific vouchers.
- Navigation links for quick access to Stores.
- Store products section uses about 2/3 of the page width.
- Store vouchers section uses about 1/3 of the page width.

## [1.2.2] - 21st June 2026
- Reworked the Marketplace into a responsive two-column layout with filters on the left and products on the right.
- Added clear filter labels, an active-filter reset link, and a visible product result count.
- Kept the product grid responsive by collapsing the layout for tablet and mobile screens.

## [1.2.3] - 21st June 2026
- Added reusable Flaticon UIcons to the shared navigation and authentication actions.
- Added icons to merchant and admin sidebars, dashboard headings, summary cards, homepage calls to action, and category shortcuts.
- Displayed each category image and added uploaded or initials-based logos for merchant stores.
- Added Flaticon attribution and kept decorative icons hidden from screen readers.

## [1.2.4] - 21st June 2026
- Added a CSRF-protected Add to cart action to each available marketplace product card.
- Expanded product cards with category, rating, review count, package size, seller, price, and stock information.
- Disabled the cart action for products that are out of stock.
- Prevented repeated add-to-cart actions from exceeding the product's available stock.
- Added the same Add to cart action to featured products on the homepage.
- Made marketplace and homepage product images link directly to their product detail pages.
- Made cart additions atomic and hardened store-logo cleanup and path validation based on review feedback.

## [1.2.5] - 21st June 2026
- Reworked the recipe listing into a responsive two-column layout with filters on the left and recipes on the right.
- Added labeled recipe filters, an active-filter reset link, a visible result count, and styled pagination.
- Kept the filter sidebar sticky on desktop and stacked it above results on smaller screens.
- Added null-safe fallbacks to recipe, product, homepage, and merchant dashboard views based on review feedback.

## [1.2.6] - 22nd June 2026
- Added a recipe count showing how many cards are displayed on the current page out of the total filtered results.
- Added null-safe dashboard statistics, request/order data, and recipe filter values based on review feedback.

## [1.2.7] - 22nd June 2026
- Added item subtotal, delivery cost, and total amount rows to the Recipe-to-Cart preview and shopping cart summaries.
- Added a fixed per-store delivery fee calculation so carts with multiple merchant stores charge delivery once per store.
- Included delivery fees in checkout totals, saved merchant order totals, and order detail displays.
- Added tests for delivery fee calculation and summary display coverage.

## [1.2.8] - 22nd June 2026
- Added average user ratings and review counts to recipe listing cards.
- Added a clear unrated state for recipes that do not have any visible reviews yet.
- Reworked the recipe details page with a two-column image and summary layout.
- Added dedicated ingredient and numbered instruction cards with serving-based quantity scaling.
- Refined instruction parsing and serving input behavior based on review feedback.

## [1.2.9] - 22nd June 2026
- Changed the customer profile page into an account overview with profile details, quick stats, shortcut actions, merchant application access, and recent orders.
- Moved profile editing into a dedicated Edit Profile page.
- Added account activity count helpers for orders, saved recipes, and reviews.

## [1.3.0] - 22nd June 2026
- Moved voucher application from checkout to each merchant section of the shopping cart.
- Added support for applying and removing multiple vouchers with server-calculated discounts and totals.
- Revalidated voucher status, dates, minimum spend, and usage limits during checkout.
- Added order-level voucher history through the `merchant_order_vouchers` migration.
- Prevented invalid merchant voucher date ranges with strict server validation and linked browser date constraints.
- Hardened stacked voucher validation, blank date handling, and transactional stock updates based on review feedback.

## [1.3.1] - 22nd June 2026
- Expanded the cart order summary with estimated subtotal, delivery cost, and total amount.
- Added a continue-shopping action to the cart so customers can return to browsing products.
- Reworked customer order history into responsive order cards with status badges, item counts, dates, totals, payment status, and view-detail actions.