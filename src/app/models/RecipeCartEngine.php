<?php
declare(strict_types=1);
namespace App\Models;

/**
 * Deterministic Recipe-to-Cart engine.
 *
 * Inputs:
 *   - Recipe ID + selected servings.
 * Outputs:
 *   - Array of "preview" line items grouped by merchant/store.
 *   - Array of warnings for ingredients with no match / unit issue / stock issue.
 */
class RecipeCartEngine
{
    public function __construct(
        private Recipe $recipes = new Recipe(),
        private RecipeIngredient $ingredients = new RecipeIngredient(),
        private Product $products = new Product()
    ) {
    }

    /**
     * @return array{items: array<int, array>, warnings: array<int, string>, recipe: array|null}
     */
    public function generate(int $recipeId, int $selectedServings): array
    {
        $recipe = $this->recipes->find($recipeId);
        if (!$recipe || $recipe['status'] !== 'active') {
            return ['items' => [], 'warnings' => ['Recipe not available.'], 'recipe' => null];
        }
        if ($selectedServings <= 0) {
            return ['items' => [], 'warnings' => ['Selected servings must be greater than zero.'], 'recipe' => $recipe];
        }

        $base = max(1, (int) $recipe['base_servings']);
        $scale = $selectedServings / $base;

        $rows = $this->ingredients->detailed($recipeId);
        if (!$rows) {
            return ['items' => [], 'warnings' => ['Recipe has no ingredients.'], 'recipe' => $recipe];
        }

        $items = [];
        $warnings = [];
        $usedStores = []; // track which stores already contribute

        foreach ($rows as $ri) {
            $scaledQty = (float) $ri['quantity'] * $scale;
            $unit = strtolower(trim((string) $ri['unit']));
            $baseUnit = strtolower(trim((string) $ri['base_unit']));

            $candidates = $this->products->activeByIngredient((int) $ri['ingredient_id']);
            if (!$candidates) {
                $warnings[] = "No product available for {$ri['ingredient_name']}.";
                continue;
            }

            $ranked = [];
            foreach ($candidates as $p) {
                $pUnit = strtolower(trim((string) $p['package_unit']));
                if ($baseUnit !== '' && $pUnit !== '' && $pUnit !== $baseUnit && $pUnit !== $unit) {
                    // Unit mismatch — skip but remember.
                    $warnings[] = "Unit mismatch for {$ri['ingredient_name']} (recipe {$unit} vs product {$pUnit}).";
                    continue;
                }
                $pkgQty = max(0.01, (float) $p['package_quantity']);
                $required = (int) ceil($scaledQty / $pkgQty);
                if ($required < 1)
                    $required = 1;

                if ((int) $p['stock_quantity'] < $required) {
                    $warnings[] = "Insufficient stock at {$p['store_name']} for {$ri['ingredient_name']}.";
                    continue;
                }

                $excess = ($required * $pkgQty) - $scaledQty;
                $cost = $required * (float) $p['price'];
                $ranked[] = [
                    'p' => $p,
                    'required' => $required,
                    'excess' => $excess,
                    'cost' => $cost,
                ];
            }

            if (!$ranked) {
                $warnings[] = "No suitable product for {$ri['ingredient_name']}.";
                continue;
            }

            // Product ranking: fewest packages → lowest excess → lowest cost → highest rating → lowest product_id.
            // Merchant preference: stores already in cart bubble up.
            usort($ranked, function ($a, $b) use ($usedStores) {
                $aIn = isset($usedStores[(int) $a['p']['store_id']]) ? 0 : 1;
                $bIn = isset($usedStores[(int) $b['p']['store_id']]) ? 0 : 1;
                if (($cmp = $aIn <=> $bIn) !== 0)
                    return $cmp;
                if (($cmp = $a['required'] <=> $b['required']) !== 0)
                    return $cmp;
                if (($cmp = $a['excess'] <=> $b['excess']) !== 0)
                    return $cmp;
                if (($cmp = $a['cost'] <=> $b['cost']) !== 0)
                    return $cmp;
                $ar = (float) ($a['p']['rating'] ?? 0);
                $br = (float) ($b['p']['rating'] ?? 0);
                if (($cmp = $br <=> $ar) !== 0)
                    return $cmp;
                return (int) $a['p']['product_id'] <=> (int) $b['p']['product_id'];
            });

            $best = $ranked[0];
            $usedStores[(int) $best['p']['store_id']] = true;
            $items[] = [
                'recipe_ingredient_id' => (int) $ri['recipe_ingredient_id'],
                'ingredient_name' => $ri['ingredient_name'],
                'scaled_quantity' => round($scaledQty, 2),
                'unit' => $ri['unit'],
                'product' => $best['p'],
                'required_packages' => $best['required'],
                'line_total' => round($best['cost'], 2),
            ];
        }

        return ['items' => $items, 'warnings' => $warnings, 'recipe' => $recipe];
    }

    /** Group items by store_id for preview display. */
    public function groupByStore(array $items): array
    {
        $out = [];
        foreach ($items as $it) {
            $sid = (int) $it['product']['store_id'];
            $out[$sid]['store_name'] = $it['product']['store_name'];
            $out[$sid]['items'][] = $it;
            $out[$sid]['subtotal'] = ($out[$sid]['subtotal'] ?? 0) + $it['line_total'];
        }
        return $out;
    }
}
