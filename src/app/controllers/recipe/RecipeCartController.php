<?php
declare(strict_types=1);
namespace App\Controllers\Recipe;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\RecipeCartEngine;
use App\Models\Cart;
use App\Models\CartItem;

class RecipeCartController extends Controller
{
    public function preview(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $servings = (int) $this->input('servings', 1);
        $engine = new RecipeCartEngine();
        $result = $engine->generate((int) $id, $servings);
        $grouped = $engine->groupByStore($result['items']);
        $this->view('recipe/cart-preview', [
            'title'    => 'Cart Preview',
            'recipe'   => $result['recipe'],
            'items'    => $result['items'],
            'warnings' => $result['warnings'],
            'grouped'  => $grouped,
            'servings' => $servings,
        ]);
    }

    public function confirm(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();
        $servings = (int) $this->input('servings', 1);
        $engine = new RecipeCartEngine();
        $result = $engine->generate((int) $id, $servings);
        if (!$result['items']) {
            Flash::set('error', 'Nothing could be added from this recipe.');
            $this->redirect('/recipes/' . $id);
        }
        $cart = (new Cart())->findOrCreateForUser((int) AuthHelper::id());
        $ci = new CartItem();
        foreach ($result['items'] as $it) {
            $ci->addOrIncrement(
                (int) $cart['cart_id'],
                (int) $it['product']['product_id'],
                (int) $it['required_packages'],
                (float) $it['product']['price'],
                'recipe',
                (int) $id,
                (int) $it['recipe_ingredient_id']
            );
        }
        Flash::set('success', 'Recipe ingredients added to cart.');
        $this->redirect('/cart');
    }
}
