<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/helper/Autoload.php';
require_once __DIR__ . '/../src/app/helper/Model.php';

use App\Models\RecipeCartEngine;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Product;

/**
 * Pure-logic test of the deterministic Recipe-to-Cart engine.
 * Uses stubbed model classes so PDO is not required.
 */
class RecipeCartEngineTest extends TestCase
{
    public function test_scales_and_picks_fewest_packages_first(): void
    {
        $recipe = new class extends Recipe {
            public function find($id): ?array
            {
                return ['recipe_id' => 1, 'base_servings' => 2, 'status' => 'active'];
            }
        };
        $ri = new class extends RecipeIngredient {
            public function detailed(int $r): array
            {
                return [
                    [
                        'recipe_ingredient_id' => 10,
                        'ingredient_id' => 1,
                        'ingredient_name' => 'Rice',
                        'base_unit' => 'g',
                        'quantity' => 200,
                        'unit' => 'g',
                    ]
                ];
            }
        };
        $prod = new class extends Product {
            public function activeByIngredient(int $iid): array
            {
                return [
                    ['product_id' => 1, 'store_id' => 1, 'store_name' => 'A', 'product_name' => 'Rice 500g', 'price' => 5, 'stock_quantity' => 50, 'package_quantity' => 500, 'package_unit' => 'g', 'rating' => 4.0],
                    ['product_id' => 2, 'store_id' => 2, 'store_name' => 'B', 'product_name' => 'Rice 1kg', 'price' => 9, 'stock_quantity' => 50, 'package_quantity' => 1000, 'package_unit' => 'g', 'rating' => 4.5],
                ];
            }
        };

        $engine = new RecipeCartEngine($recipe, $ri, $prod);
        // Scale x2 = 400g needed. 1kg pack -> 1 pkg, 500g pack -> 1 pkg.
        // Tie on packages, then excess: 1000-400=600 vs 500-400=100 -> 500g wins.
        $out = $engine->generate(1, 4);
        $this->assertCount(1, $out['items']);
        $this->assertSame(1, $out['items'][0]['product']['product_id']);
        $this->assertSame(1, $out['items'][0]['required_packages']);
    }

    public function test_warns_when_no_product(): void
    {
        $recipe = new class extends Recipe {
            public function find($id): ?array
            {
                return ['recipe_id' => 1, 'base_servings' => 1, 'status' => 'active'];
            }
        };
        $ri = new class extends RecipeIngredient {
            public function detailed(int $r): array
            {
                return [['recipe_ingredient_id' => 1, 'ingredient_id' => 9, 'ingredient_name' => 'X', 'base_unit' => 'g', 'quantity' => 100, 'unit' => 'g']];
            }
        };
        $prod = new class extends Product {
            public function activeByIngredient(int $iid): array
            {
                return [];
            }
        };
        $out = (new RecipeCartEngine($recipe, $ri, $prod))->generate(1, 1);
        $this->assertEmpty($out['items']);
        $this->assertNotEmpty($out['warnings']);
    }

    public function test_zero_servings_warns(): void
    {
        $recipe = new class extends Recipe {
            public function find($id): ?array
            {
                return ['recipe_id' => 1, 'base_servings' => 2, 'status' => 'active'];
            }
        };
        $ri = new class extends RecipeIngredient {
            public function detailed(int $r): array
            {
                return [];
            }
        };
        $prod = new class extends Product {
            public function activeByIngredient(int $iid): array
            {
                return [];
            }
        };
        $out = (new RecipeCartEngine($recipe, $ri, $prod))->generate(1, 0);
        $this->assertSame(['Selected servings must be greater than zero.'], $out['warnings']);
    }

    public function test_cost_rating_and_id_tie_breakers_are_deterministic(): void
    {
        $recipe = new class extends Recipe {
            public function find($id): ?array
            {
                return ['recipe_id' => 1, 'base_servings' => 1, 'status' => 'active'];
            }
        };
        $ri = new class extends RecipeIngredient {
            public function detailed(int $r): array
            {
                return [['recipe_ingredient_id' => 1, 'ingredient_id' => 1, 'ingredient_name' => 'Eggs', 'base_unit' => 'pcs', 'quantity' => 6, 'unit' => 'pcs']];
            }
        };
        $prod = new class extends Product {
            public function activeByIngredient(int $iid): array
            {
                return [
                    ['product_id' => 9, 'store_id' => 1, 'store_name' => 'A', 'product_name' => 'Eggs A', 'price' => 6, 'stock_quantity' => 10, 'package_quantity' => 6, 'package_unit' => 'pcs', 'rating' => 4.0],
                    ['product_id' => 8, 'store_id' => 2, 'store_name' => 'B', 'product_name' => 'Eggs B', 'price' => 6, 'stock_quantity' => 10, 'package_quantity' => 6, 'package_unit' => 'pcs', 'rating' => 4.8],
                    ['product_id' => 7, 'store_id' => 3, 'store_name' => 'C', 'product_name' => 'Eggs C', 'price' => 7, 'stock_quantity' => 10, 'package_quantity' => 6, 'package_unit' => 'pcs', 'rating' => 5.0],
                ];
            }
        };

        $out = (new RecipeCartEngine($recipe, $ri, $prod))->generate(1, 1);
        $this->assertSame(8, $out['items'][0]['product']['product_id']);

        $repeat = (new RecipeCartEngine($recipe, $ri, $prod))->generate(1, 1);
        $this->assertSame($out['items'][0]['product']['product_id'], $repeat['items'][0]['product']['product_id']);
    }

    public function test_insufficient_stock_is_excluded(): void
    {
        $recipe = new class extends Recipe {
            public function find($id): ?array
            {
                return ['recipe_id' => 1, 'base_servings' => 1, 'status' => 'active'];
            }
        };
        $ri = new class extends RecipeIngredient {
            public function detailed(int $r): array
            {
                return [['recipe_ingredient_id' => 1, 'ingredient_id' => 1, 'ingredient_name' => 'Milk', 'base_unit' => 'ml', 'quantity' => 1000, 'unit' => 'ml']];
            }
        };
        $prod = new class extends Product {
            public function activeByIngredient(int $iid): array
            {
                return [
                    ['product_id' => 1, 'store_id' => 1, 'store_name' => 'A', 'product_name' => 'Milk 500', 'price' => 2, 'stock_quantity' => 1, 'package_quantity' => 500, 'package_unit' => 'ml', 'rating' => 5],
                    ['product_id' => 2, 'store_id' => 2, 'store_name' => 'B', 'product_name' => 'Milk 1L', 'price' => 5, 'stock_quantity' => 5, 'package_quantity' => 1000, 'package_unit' => 'ml', 'rating' => 3],
                ];
            }
        };

        $out = (new RecipeCartEngine($recipe, $ri, $prod))->generate(1, 1);
        $this->assertSame(2, $out['items'][0]['product']['product_id']);
        $this->assertNotEmpty($out['warnings']);
    }
}
