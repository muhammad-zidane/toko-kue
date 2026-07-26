<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomizationController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\ShippingZoneController as AdminShippingZoneController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', fn() => view('pages.about'))->name('about');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::post('/voucher/apply', [VoucherController::class, 'apply'])->name('voucher.apply');

// Cart — bisa diakses guest (session-based), hanya checkout yang butuh auth
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::middleware('throttle:20,1')->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update-item', [CartController::class, 'updateItem'])->name('cart.updateItem');
});

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

    // Saved Addresses
    Route::get('/account/addresses', [AddressController::class, 'index'])->name('account.addresses.index');
    Route::post('/account/addresses', [AddressController::class, 'store'])->name('account.addresses.store');
    Route::put('/account/addresses/{address}', [AddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('/account/addresses/{address}', [AddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::post('/account/addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('account.addresses.setDefault');

    // Cart checkout (butuh auth)
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Write operations (rate-limited)
    Route::middleware('throttle:20,1')->group(function () {
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/{order}/upload-proof', [OrderController::class, 'uploadProof'])->name('orders.uploadProof');
        Route::post('/orders/{order}/reviews/{product}', [ProductReviewController::class, 'store'])->name('orders.reviews.store');
        Route::patch('/orders/{order}/reviews/{review}', [ProductReviewController::class, 'update'])->name('orders.reviews.update');
    });
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/download-proof', [AdminOrderController::class, 'downloadProof'])->name('orders.downloadProof');
    Route::patch('/orders/{order}/status/{status}', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    // Products
    Route::resource('products', ProductController::class)->except(['index', 'show']);
    Route::get('/products-list', [AdminController::class, 'adminProducts'])->name('products.index');

    // Categories
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Other pages
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers.index');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics.index');
    Route::get('/analytics/export', [AdminController::class, 'exportLaporan'])->name('analytics.export');
    Route::get('/finance', [AdminController::class, 'finance'])->name('finance.index');
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

    // Banners
    Route::get('/banners', [AdminBannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [AdminBannerController::class, 'store'])->name('banners.store');
    Route::put('/banners/{banner}', [AdminBannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->name('banners.destroy');

    // Vouchers
    Route::get('/vouchers', [AdminVoucherController::class, 'index'])->name('vouchers.index');
    Route::post('/vouchers', [AdminVoucherController::class, 'store'])->name('vouchers.store');
    Route::put('/vouchers/{voucher}', [AdminVoucherController::class, 'update'])->name('vouchers.update');
    Route::delete('/vouchers/{voucher}', [AdminVoucherController::class, 'destroy'])->name('vouchers.destroy');

    // Shipping Zones
    Route::get('/shipping-zones', [AdminShippingZoneController::class, 'index'])->name('shipping-zones.index');
    Route::post('/shipping-zones', [AdminShippingZoneController::class, 'store'])->name('shipping-zones.store');
    Route::put('/shipping-zones/{zone}', [AdminShippingZoneController::class, 'update'])->name('shipping-zones.update');
    Route::delete('/shipping-zones/{zone}', [AdminShippingZoneController::class, 'destroy'])->name('shipping-zones.destroy');

    // Production Calendar
    Route::get('/production-calendar', [AdminController::class, 'productionCalendar'])->name('production-calendar.index');

    // Reviews moderation
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Payment confirm/reject
    Route::post('/orders/{order}/confirm-payment', [AdminOrderController::class, 'confirmPayment'])->name('orders.confirmPayment');
    Route::post('/orders/{order}/reject-payment', [AdminOrderController::class, 'rejectPayment'])->name('orders.rejectPayment');

    // Notifications
    Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.read');

    // Customization options
    Route::get('/customizations', [CustomizationController::class, 'index'])->name('customizations.index');
    Route::post('/customizations', [CustomizationController::class, 'store'])->name('customizations.store');
    Route::put('/customizations/{option}', [CustomizationController::class, 'update'])->name('customizations.update');
    Route::post('/customizations/{option}/toggle', [CustomizationController::class, 'toggle'])->name('customizations.toggle');
    Route::delete('/customizations/{option}', [CustomizationController::class, 'destroy'])->name('customizations.destroy');
});

// PDF Invoice (authenticated)
Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->middleware('auth')->name('orders.invoice');
