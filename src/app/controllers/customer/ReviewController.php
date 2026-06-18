<?php
declare(strict_types=1);
namespace App\Controllers\Customer;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Review;

class ReviewController extends Controller
{
    public function storeProduct(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();

        $rating = (int) $this->input('rating', 5);
        $comment = trim((string) $this->input('comment', ''));

        $review = new Review();
        $existing = $review->findForProductByUser((int) AuthHelper::id(), (int) $id);
        $review->saveProductReview((int) AuthHelper::id(), (int) $id, $rating, $comment);
        Flash::set('success', $existing ? 'Review updated.' : 'Review submitted.');
        $this->redirect('/products/' . $id);
    }

    public function storeRecipe(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();

        $rating = (int) $this->input('rating', 5);
        $comment = trim((string) $this->input('comment', ''));

        $review = new Review();
        $existing = $review->findForRecipeByUser((int) AuthHelper::id(), (int) $id);
        $review->saveRecipeReview((int) AuthHelper::id(), (int) $id, $rating, $comment);
        Flash::set('success', $existing ? 'Recipe review updated.' : 'Recipe review submitted.');
        $this->redirect('/recipes/' . $id);
    }
}
