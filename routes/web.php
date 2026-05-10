<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductGroupController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductVariationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController as CustomerOrderController;
use App\Http\Controllers\ShopController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public shop API
Route::prefix('api')->middleware('throttle:60,1')->group(function () {
    Route::get('/products', [ShopController::class, 'products']);
    Route::get('/products/{slug}', [ShopController::class, 'show']);
    Route::get('/categories', [ShopController::class, 'categories']);
    Route::get('/categories/{slug}', [ShopController::class, 'categoryBySlug']);
    Route::get('/settings', [SettingController::class, 'publicIndex']);
});

// Favorites API
Route::prefix('api/favorites')->group(function () {
    Route::get('/',           [FavoriteController::class, 'index']);
    Route::post('/{product}', [FavoriteController::class, 'toggle']);
});

// Customer Orders API
Route::prefix('api/orders')->group(function () {
    Route::post('/',       [CustomerOrderController::class, 'store']);  // public: guest checkout
    Route::get('/{order}', [CustomerOrderController::class, 'show']);   // public: guest order confirmation
});
Route::middleware('auth')->prefix('api/orders')->group(function () {
    Route::get('/', [CustomerOrderController::class, 'index']);
});

// Customer Auth API
Route::prefix('api/auth')->group(function () {
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/register', [CustomerAuthController::class, 'register']);
        Route::post('/login',    [CustomerAuthController::class, 'login']);
    });
    Route::post('/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/me',      [CustomerAuthController::class, 'me']);
});

// Customer SPA routes
Route::get('/shop',        fn () => view('welcome'));
Route::get('/product/{slug}', fn () => view('welcome'));
Route::get('/login',       fn () => view('welcome'));
Route::get('/register',    fn () => view('welcome'));
Route::get('/account',     fn () => view('welcome'));
Route::get('/orders',      fn () => view('welcome'));
Route::get('/orders/{id}', fn () => view('welcome'));
Route::get('/category/{slug}', fn () => view('welcome'));
Route::get('/favorites',   fn () => view('welcome'));
Route::get('/cart',        fn () => view('welcome'));
Route::get('/checkout',    fn () => view('welcome'));
Route::get('/order/confirmation', fn () => view('welcome'));

// Admin SPA — HTML shell is public; security is enforced on /api/admin/* routes
Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/{any}', [AdminController::class, 'index'])->where('any', '.*');

// Admin API
Route::prefix('api/admin')->group(function () {
    Route::middleware('throttle:5,1')->post('/login', [AuthController::class, 'login']);

    Route::middleware(EnsureUserIsAdmin::class)->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Products
        Route::apiResource('products', ProductController::class);

        // Product gallery images
        Route::post('products/{product}/images', [ProductImageController::class, 'store']);
        Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy']);

        // Product variations (nested)
        Route::get('products/{product}/variations', [ProductVariationController::class, 'index']);
        Route::post('products/{product}/variations/generate', [ProductVariationController::class, 'generate']);
        Route::put('products/{product}/variations/{variation}', [ProductVariationController::class, 'update']);
        Route::delete('products/{product}/variations/{variation}', [ProductVariationController::class, 'destroy']);
        Route::post('products/{product}/variations/{variation}/image', [ProductVariationController::class, 'uploadImage']);
        Route::delete('products/{product}/variations/{variation}/image', [ProductVariationController::class, 'removeImage']);

        // Product group items (nested)
        Route::get('products/{product}/group-items', [ProductGroupController::class, 'index']);
        Route::post('products/{product}/group-items', [ProductGroupController::class, 'store']);
        Route::delete('products/{product}/group-items/{childId}', [ProductGroupController::class, 'destroy']);

        // Attributes
        Route::apiResource('attributes', AttributeController::class)->except(['show']);
        Route::post('attributes/{attribute}/values', [AttributeController::class, 'addValue']);
        Route::delete('attributes/{attribute}/values/{value}', [AttributeController::class, 'removeValue']);

        // Categories, Orders, Users
        Route::apiResource('categories', CategoryController::class);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
        Route::get('/users', [UserController::class, 'index']);

        // General settings
        Route::get('/settings',  [SettingController::class, 'index']);
        Route::post('/settings', [SettingController::class, 'update']);
    });
});
