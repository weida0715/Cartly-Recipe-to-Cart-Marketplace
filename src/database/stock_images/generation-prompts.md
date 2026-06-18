# Stock Image Generation Prompts

These prompts document the repeatable image-generation process for the seeded
Cartly stock images. Generate grid images first, save the originals under
`src/database/stock_images/generated_grids/`, then crop or export the needed
tiles into the matching folders under `src/database/stock_images/`.

## Ingredient Grid

Create a clean 4 by 4 grid of square stock-style grocery ingredient images for a
recipe marketplace. Include rice, chicken breast, egg, tomato, onion, garlic,
salt, olive oil, milk, butter, potato, carrot, coffee, vegetables, dairy, and
pantry items. Each tile should be isolated on a simple light background with no
text, no logos, no watermarks, and consistent bright marketplace lighting.

## Recipe Grid

Create a clean 3 by 2 grid of square plated recipe stock images for a
recipe-to-cart marketplace. Include chicken fried rice, tomato egg stir fry,
creamy mashed potatoes, homemade carrot soup, morning coffee blend, and a simple
fresh grocery basket. Each tile should look appetizing, bright, isolated on a
simple tabletop, with no text, no logos, no watermarks.

## Seed Asset Flow

1. Save the generated grid originals under `src/database/stock_images/generated_grids/`.
2. Save ingredient tiles under `src/database/stock_images/ingredient/<name>/`.
3. Save recipe tiles under `src/database/stock_images/recipe/<name>/`.
4. Run `php src/database/seed_assets.php` from the project root.
5. Import or re-import `src/database/seed.sql` so seeded database rows point to
   the copied upload paths.
