<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Helpers\Controller;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(): void
    {
        $featured = array_slice((new Product())->active(), 0, 8);
        $recipes  = array_slice((new Recipe())->active(), 0, 6);
        $cats     = (new Category())->active();
        $this->view('customer/home', [
            'title'    => 'Cartly · Recipe-to-Cart Marketplace',
            'featured' => $featured,
            'recipes'  => $recipes,
            'cats'     => $cats,
        ]);
    }
}
