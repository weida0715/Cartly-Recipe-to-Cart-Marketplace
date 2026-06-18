<?php
declare(strict_types=1);
namespace App\Controllers\Product;

use App\Helpers\Controller;
use App\Helpers\AuthHelper;
use App\Helpers\Flash;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\CartItem;

class ProductController extends Controller
{
    public function index(): void
    {
        $q = trim((string) $this->input('q', ''));
        $cid = (int) $this->input('cid', 0) ?: null;
        $sort = (string) $this->input('sort', 'newest');
        $page = max(1, (int) $this->input('page', 1));
        $result = (new Product())->paginateActive($q, $cid, $sort, $page, 16);
        $this->view('product/index', [
            'title' => 'Marketplace · Cartly',
            'products' => $result['rows'],
            'cats' => (new Category())->active(),
            'q' => $q,
            'cid' => $cid,
            'sort' => $sort,
            'page' => $result['page'],
            'pages' => $result['pages'],
            'total' => $result['total'],
            'cartCount' => AuthHelper::check()
                ? (new CartItem())->countForUser((int) AuthHelper::id())
                : 0,
        ]);
    }

    public function show(string $id): void
    {
        $product = (new Product())->findWithStore((int) $id);
        if (!$product) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }
        $review = new Review();
        $this->view('product/show', [
            'title' => $product['product_name'] . ' · Cartly',
            'product' => $product,
            'reviews' => $review->forProduct((int) $id),
            'currentUserReview' => AuthHelper::check()
                ? $review->findForProductByUser((int) AuthHelper::id(), (int) $id)
                : null,
        ]);
    }

    public function report(string $id): void
    {
        AuthHelper::requireLogin();
        Flash::set('info', 'Use the report form on this product page.');
        $this->redirect('/products/' . $id);
    }
}
