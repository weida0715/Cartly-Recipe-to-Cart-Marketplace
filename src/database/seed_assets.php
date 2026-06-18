<?php
declare(strict_types=1);

/**
 * Copies versioned stock seed images into the public upload folder.
 *
 * Run from the project root after importing the database seed:
 * php src/database/seed_assets.php
 */

function cartly_seed_asset_manifest(): array
{
    return [
        'categories' => [
            ['source' => 'category/vegetables/vegetables.png', 'target' => 'seeded/categories/vegetables.png'],
            ['source' => 'category/meat/meat.png', 'target' => 'seeded/categories/meat.png'],
            ['source' => 'category/dairy/dairy.png', 'target' => 'seeded/categories/dairy.png'],
            ['source' => 'category/grains/grains.png', 'target' => 'seeded/categories/grains.png'],
            ['source' => 'category/pantry/pantry.png', 'target' => 'seeded/categories/pantry.png'],
            ['source' => 'category/frozen/frozen.png', 'target' => 'seeded/categories/frozen.png'],
            ['source' => 'category/beverages/beverages.png', 'target' => 'seeded/categories/beverages.png'],
        ],
        'products' => [
            ['source' => 'ingredient/rice/rice.png', 'target' => 'seeded/products/rice.png'],
            ['source' => 'ingredient/chicken_breast/chicken-breast.png', 'target' => 'seeded/products/chicken-breast.png'],
            ['source' => 'ingredient/egg/egg.png', 'target' => 'seeded/products/egg.png'],
            ['source' => 'ingredient/tomato/tomato.png', 'target' => 'seeded/products/tomato.png'],
            ['source' => 'ingredient/onion/onion.png', 'target' => 'seeded/products/onion.png'],
            ['source' => 'ingredient/garlic/garlic.png', 'target' => 'seeded/products/garlic.png'],
            ['source' => 'ingredient/salt/salt.png', 'target' => 'seeded/products/salt.png'],
            ['source' => 'ingredient/olive_oil/olive-oil.png', 'target' => 'seeded/products/olive-oil.png'],
            ['source' => 'ingredient/milk/milk.png', 'target' => 'seeded/products/milk.png'],
            ['source' => 'ingredient/butter/butter.png', 'target' => 'seeded/products/butter.png'],
            ['source' => 'ingredient/potato/potato.png', 'target' => 'seeded/products/potato.png'],
            ['source' => 'ingredient/carrot/carrot.png', 'target' => 'seeded/products/carrot.png'],
            ['source' => 'ingredient/coffee/coffee.png', 'target' => 'seeded/products/coffee.png'],
        ],
        'recipes' => [
            ['source' => 'recipe/chicken_fried_rice/chicken-fried-rice.png', 'target' => 'seeded/recipes/chicken-fried-rice.png'],
            ['source' => 'recipe/tomato_egg_stir_fry/tomato-egg-stir-fry.png', 'target' => 'seeded/recipes/tomato-egg-stir-fry.png'],
            ['source' => 'recipe/creamy_mashed_potatoes/creamy-mashed-potatoes.png', 'target' => 'seeded/recipes/creamy-mashed-potatoes.png'],
            ['source' => 'recipe/homemade_carrot_soup/homemade-carrot-soup.png', 'target' => 'seeded/recipes/homemade-carrot-soup.png'],
            ['source' => 'recipe/morning_coffee_blend/morning-coffee-blend.png', 'target' => 'seeded/recipes/morning-coffee-blend.png'],
        ],
    ];
}

function cartly_seed_asset_project_root(): string
{
    return dirname(__DIR__, 2);
}

function cartly_seed_asset_stock_root(): string
{
    return __DIR__ . DIRECTORY_SEPARATOR . 'stock_images';
}

function cartly_seed_asset_copy_all(?string $projectRoot = null): array
{
    $projectRoot = $projectRoot ?? cartly_seed_asset_project_root();
    $stockRoot = cartly_seed_asset_stock_root();
    $uploadRoot = $projectRoot . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads';
    $copied = [];

    foreach (cartly_seed_asset_manifest() as $group => $assets) {
        foreach ($assets as $asset) {
            $source = $stockRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $asset['source']);
            $target = $uploadRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $asset['target']);

            if (!is_file($source)) {
                throw new RuntimeException("Missing seed stock image: {$asset['source']}");
            }

            $targetDir = dirname($target);
            if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
                throw new RuntimeException("Unable to create seed upload directory: {$targetDir}");
            }

            if (!copy($source, $target)) {
                throw new RuntimeException("Unable to copy seed stock image: {$asset['source']}");
            }

            $copied[] = [
                'group' => $group,
                'source' => $asset['source'],
                'target' => $asset['target'],
            ];
        }
    }

    return $copied;
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        $copied = cartly_seed_asset_copy_all();
        foreach ($copied as $asset) {
            echo "Copied {$asset['source']} -> uploads/{$asset['target']}" . PHP_EOL;
        }
        echo 'Seed images ready.' . PHP_EOL;
    } catch (Throwable $exception) {
        fwrite(STDERR, $exception->getMessage() . PHP_EOL);
        exit(1);
    }
}
