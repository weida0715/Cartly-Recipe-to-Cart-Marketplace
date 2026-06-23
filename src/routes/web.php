<?php
declare(strict_types=1);

/** @var \App\Helpers\Router $router */

use App\Controllers\HomeController;
use App\Controllers\Auth\AuthController;
use App\Controllers\Product\ProductController;
use App\Controllers\Store\StoreController;
use App\Controllers\Recipe\RecipeController;
use App\Controllers\Recipe\RecipeCartController;
use App\Controllers\Order\CartController;
use App\Controllers\Order\CheckoutController;
use App\Controllers\Order\OrderController;
use App\Controllers\Customer\CustomerDashboardController;
use App\Controllers\Customer\ReviewController;
use App\Controllers\Customer\ReportController;
use App\Controllers\Merchant\MerchantDashboardController;
use App\Controllers\Merchant\MerchantProductController;
use App\Controllers\Merchant\MerchantOrderController;
use App\Controllers\Merchant\MerchantVoucherController;
use App\Controllers\Merchant\MerchantStoreController;
use App\Controllers\Admin\AdminDashboardController;
use App\Controllers\Admin\AdminUserController;
use App\Controllers\Admin\AdminMerchantController;
use App\Controllers\Admin\AdminCategoryController;
use App\Controllers\Admin\AdminReportController;
use App\Controllers\Admin\AdminSettingsController;

// Public
$router->get('/', [HomeController::class, 'index']);

// Auth
$router->get('/auth/login', [AuthController::class, 'loginForm']);
$router->post('/auth/login', [AuthController::class, 'login']);
$router->get('/auth/register', [AuthController::class, 'registerForm']);
$router->post('/auth/register', [AuthController::class, 'register']);
$router->get('/auth/forgot-password', [AuthController::class, 'forgotForm']);
$router->post('/auth/forgot-password', [AuthController::class, 'forgot']);
$router->get('/auth/reset-password', [AuthController::class, 'resetForm']);
$router->post('/auth/reset-password', [AuthController::class, 'reset']);
$router->get('/auth/logout', [AuthController::class, 'logout']);

// Marketplace / products
$router->get('/vouchers', [ProductController::class, 'vouchers']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/{id}', [ProductController::class, 'show']);
$router->get('/stores', [StoreController::class, 'index']);
$router->get('/stores/{id}', [StoreController::class, 'show']);

// Recipes
$router->get('/recipes', [RecipeController::class, 'index']);
$router->get('/recipes/create', [RecipeController::class, 'create']);
$router->post('/recipes', [RecipeController::class, 'store']);
$router->get('/recipes/{id}', [RecipeController::class, 'show']);
$router->get('/recipes/{id}/edit', [RecipeController::class, 'edit']);
$router->post('/recipes/{id}/update', [RecipeController::class, 'update']);
$router->post('/recipes/{id}/hide', [RecipeController::class, 'hide']);
$router->post('/recipes/{id}/save', [RecipeController::class, 'save']);
$router->post('/recipes/{id}/reviews', [ReviewController::class, 'storeRecipe']);

// Recipe -> Cart
$router->post('/recipes/{id}/preview-cart', [RecipeCartController::class, 'preview']);
$router->post('/recipes/{id}/confirm-cart', [RecipeCartController::class, 'confirm']);

// Cart
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/vouchers/apply', [CartController::class, 'applyVoucher']);
$router->post('/cart/vouchers/remove', [CartController::class, 'removeVoucher']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/clear', [CartController::class, 'clear']);

// Reviews / reports
$router->post('/products/{id}/reviews', [ReviewController::class, 'storeProduct']);
$router->post('/reports', [ReportController::class, 'store']);

// Checkout / orders
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout', [CheckoutController::class, 'place']);
$router->get('/orders', [OrderController::class, 'history']);
$router->get('/orders/merchant/{id}/tracking', [OrderController::class, 'trackingStatus']);
$router->post('/orders/merchant/{id}/advance', [OrderController::class, 'advanceDelivery']);
$router->post('/orders/merchant/{id}/received', [OrderController::class, 'received']);
$router->get('/orders/{id}', [OrderController::class, 'show']);
$router->get('/orders/{id}/confirmation', [OrderController::class, 'confirmation']);

// Customer dashboard
$router->get('/dashboard', [CustomerDashboardController::class, 'index']);
$router->get('/saved-recipes', [CustomerDashboardController::class, 'savedRecipes']);
$router->get('/profile', [CustomerDashboardController::class, 'profile']);
$router->get('/profile/edit', [CustomerDashboardController::class, 'editProfile']);
$router->post('/profile', [CustomerDashboardController::class, 'updateProfile']);
$router->post('/merchant/request', [CustomerDashboardController::class, 'requestMerchant']);

// Merchant
$router->get('/merchant', [MerchantDashboardController::class, 'index']);
$router->get('/merchant/products', [MerchantProductController::class, 'index']);
$router->get('/merchant/products/create', [MerchantProductController::class, 'create']);
$router->post('/merchant/products', [MerchantProductController::class, 'store']);
$router->get('/merchant/products/{id}/edit', [MerchantProductController::class, 'edit']);
$router->post('/merchant/products/{id}/update', [MerchantProductController::class, 'update']);
$router->post('/merchant/products/{id}/delete', [MerchantProductController::class, 'delete']);
$router->get('/merchant/orders', [MerchantOrderController::class, 'index']);
$router->post('/merchant/orders/{id}/status', [MerchantOrderController::class, 'updateStatus']);
$router->get('/merchant/orders/{id}/tracking', [MerchantOrderController::class, 'trackingStatus']);
$router->get('/merchant/vouchers', [MerchantVoucherController::class, 'index']);
$router->post('/merchant/vouchers', [MerchantVoucherController::class, 'store']);
$router->post('/merchant/vouchers/{id}/update', [MerchantVoucherController::class, 'update']);
$router->post('/merchant/vouchers/{id}/delete', [MerchantVoucherController::class, 'delete']);
$router->get('/merchant/store', [MerchantStoreController::class, 'edit']);
$router->post('/merchant/store', [MerchantStoreController::class, 'update']);

// Admin
$router->get('/admin', [AdminDashboardController::class, 'index']);
$router->get('/admin/users', [AdminUserController::class, 'index']);
$router->post('/admin/users/{id}/status', [AdminUserController::class, 'updateStatus']);
$router->post('/admin/users/{id}/role', [AdminUserController::class, 'updateRole']);
$router->get('/admin/merchants', [AdminMerchantController::class, 'index']);
$router->post('/admin/merchants/{id}/approve', [AdminMerchantController::class, 'approve']);
$router->post('/admin/merchants/{id}/reject', [AdminMerchantController::class, 'reject']);
$router->post('/admin/merchants/{id}/close', [AdminMerchantController::class, 'close']);
$router->get('/admin/categories', [AdminCategoryController::class, 'index']);
$router->post('/admin/categories', [AdminCategoryController::class, 'store']);
$router->post('/admin/categories/{id}/update', [AdminCategoryController::class, 'update']);
$router->post('/admin/categories/{id}/delete', [AdminCategoryController::class, 'delete']);
$router->get('/admin/settings', [AdminSettingsController::class, 'index']);
$router->post('/admin/settings', [AdminSettingsController::class, 'update']);
$router->get('/admin/reports', [AdminReportController::class, 'index']);
$router->post('/admin/reports/{id}/resolve', [AdminReportController::class, 'resolve']);

// 404
$router->notFound(function () {
    require dirname(__DIR__) . '/app/views/errors/404.php';
});
