<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductReviewController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Breeze requires a named "dashboard" route
Route::get('/dashboard', fn () => redirect()->route('home'))->middleware('auth')->name('dashboard');

require __DIR__.'/auth.php';

// Authenticated user routes
Route::middleware('auth')->group(function () {

    // Ganti Password
    Route::get('/akun/ganti-password', [AccountController::class, 'showChangePassword'])->name('account.change-password');
    Route::post('/akun/ganti-password', [AccountController::class, 'updatePassword'])->name('account.update-password');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Orders (read)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/status', [OrderController::class, 'showStatus'])->name('orders.status');
    Route::get('/orders/{order}/payment', [OrderController::class, 'payment'])->name('orders.payment');
    Route::get('/orders/{order}/success', [OrderController::class, 'success'])->name('orders.success');
    Route::get('/checkout/{product}', [OrderController::class, 'singleProductCheckout'])->name('orders.create');

    // Reviews (read/delete)
    Route::get('/orders/{order}/reviews', [ProductReviewController::class, 'index'])->name('orders.reviews.index');
    Route::delete('/orders/{order}/reviews/{review}', [ProductReviewController::class, 'destroy'])->name('orders.reviews.destroy');
    Route::delete('/orders/{order}/reviews/{review}/images/{image}', [ProductReviewController::class, 'destroyImage'])->name('orders.reviews.images.destroy');

    // Cart (read/delete)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Write operations (rate-limited)
    Route::middleware('throttle:20,1')->group(function () {
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/{order}/upload-proof', [OrderController::class, 'uploadProof'])->name('orders.uploadProof');
        Route::post('/orders/{order}/reviews/{product}', [ProductReviewController::class, 'store'])->name('orders.reviews.store');
        Route::patch('/orders/{order}/reviews/{review}', [ProductReviewController::class, 'update'])->name('orders.reviews.update');
        Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/update-item', [CartController::class, 'updateItem'])->name('cart.updateItem');
    });
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [AdminController::class, 'orderDetail'])->name('orders.show');
    Route::get('/orders/{order}/download-proof', [AdminController::class, 'downloadProof'])->name('orders.downloadProof');
    Route::patch('/orders/{order}/status/{status}', [AdminController::class, 'updateOrderStatus'])->name('orders.status');

    // Products
    Route::resource('products', ProductController::class)->except(['index', 'show']);
    Route::get('/products-list', [AdminController::class, 'adminProducts'])->name('products.index');

    // Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');

    // Other pages
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers.index');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics.index');
    Route::get('/finance', [AdminController::class, 'finance'])->name('finance.index');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});
