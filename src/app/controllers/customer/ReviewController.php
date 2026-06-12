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
        if ($comment === '') {
            Flash::set('error', 'Review comment is required.');
            $this->redirect('/products/' . $id);
        }

        (new Review())->addProductReview((int) AuthHelper::id(), (int) $id, $rating, $comment);
        Flash::set('success', 'Review submitted.');
        $this->redirect('/products/' . $id);
    }

    public function storeRecipe(string $id): void
    {
        AuthHelper::requireLogin();
        $this->requireCsrf();

        $rating = (int) $this->input('rating', 5);
        $comment = trim((string) $this->input('comment', ''));
        if ($comment === '') {
            Flash::set('error', 'Review comment is required.');
            $this->redirect('/recipes/' . $id);
        }

        (new Review())->addRecipeReview((int) AuthHelper::id(), (int) $id, $rating, $comment);
        Flash::set('success', 'Recipe review submitted.');
        $this->redirect('/recipes/' . $id);
    }
}