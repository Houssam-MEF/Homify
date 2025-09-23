<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/healthz', fn() => response()->json(['ok' => true]));


// Catalog Routes (Public)
Route::get('/', [CatalogController::class, 'home'])->name('catalog.home');
Route::get('/c/{slug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/p/{slug}', [CatalogController::class, 'product'])->name('catalog.product');
Route::get('/search', [CatalogController::class, 'search'])->name('catalog.search');

// Cart Routes (Public)
Route::prefix('panier')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'show'])->name('show');
    Route::post('/', [CartController::class, 'add'])->name('add');
    Route::patch('/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/{item}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// Checkout Routes (Auth Required)
Route::middleware('auth')->group(function () {
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'start'])->name('start');
        Route::post('/', [CheckoutController::class, 'placeOrder'])->name('place');
        Route::get('/payment', [CheckoutController::class, 'payment'])->name('payment');
        Route::post('/payment', [CheckoutController::class, 'processPayment'])->name('process-payment');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
    });

    // Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
        Route::patch('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('reorder');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Payment Routes
Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/intent', [PaymentController::class, 'createIntent'])->middleware('auth')->name('intent');
    Route::get('/{order}/success', [PaymentController::class, 'success'])->middleware('auth')->name('success');
    Route::get('/{order}/failed', [PaymentController::class, 'failed'])->middleware('auth')->name('failed');
    Route::get('/{order}/cancel', [PaymentController::class, 'cancel'])->middleware('auth')->name('cancel');
});

// Stripe Webhook (No Auth)
Route::post('/webhooks/stripe', [PaymentController::class, 'webhook'])->name('webhooks.stripe');

// Admin Access - URLs secrètes
Route::get('/admin-access', [\App\Http\Controllers\Admin\AdminAccessController::class, 'showAccessForm'])->name('admin.access');
Route::post('/admin-access', [\App\Http\Controllers\Admin\AdminAccessController::class, 'verifyAccess'])->name('admin.verify-access');
Route::get('/admin-access-{token}', [\App\Http\Controllers\Admin\AdminAccessController::class, 'directAccess'])->name('admin.direct-access');

// Admin Routes - URL complexe pour la sécurité
Route::middleware(['auth', 'admin'])->prefix('system-management-2024')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        return redirect()->route('admin.orders.index');
    })->name('dashboard');

    // Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{order}/confirm', [\App\Http\Controllers\Admin\OrderController::class, 'confirm'])->name('confirm');
        Route::patch('/{order}/ship', [\App\Http\Controllers\Admin\OrderController::class, 'ship'])->name('ship');
        Route::patch('/{order}/deliver', [\App\Http\Controllers\Admin\OrderController::class, 'deliver'])->name('deliver');
        Route::patch('/{order}/cancel', [\App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('cancel');
    });

    // Categories Management
    Route::resource('categories', AdminCategoryController::class);
    Route::patch('/categories/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::get('/api/categories', [AdminCategoryController::class, 'apiIndex'])->name('api.categories');

    // Products Management
    Route::resource('products', AdminProductController::class);
    Route::patch('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('/products/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
    Route::post('/products/bulk-update-stock', [AdminProductController::class, 'bulkUpdateStock'])->name('products.bulk-update-stock');
    Route::delete('/product-images/{image}', [AdminProductController::class, 'deleteImage'])->name('product-images.destroy');
    Route::patch('/products/{product}/images/order', [AdminProductController::class, 'updateImageOrder'])->name('products.images.order');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])->name('toggle-admin');
        Route::patch('/{user}/password', [AdminUserController::class, 'updatePassword'])->name('update-password');
        Route::get('/statistics/overview', [AdminUserController::class, 'statistics'])->name('statistics');
    });
});

require __DIR__.'/auth.php';
