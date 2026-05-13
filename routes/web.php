<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

// Halaman utama
Route::get('/', function() {
    $categories = \App\Models\Category::withCount('products')->get();
    $featuredProducts = \App\Models\Product::where('is_available', true)->take(3)->get();
    $testimonials = \App\Models\Testimonial::latest()->take(3)->get();
    return view('home.index', compact('categories', 'featuredProducts', 'testimonials'));
})->name('home');

// Dashboard redirect (dibutuhkan oleh Breeze navigation)
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

// Produk (publik)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Auth routes (dari Breeze)
require __DIR__.'/auth.php';

// Routes untuk user yang sudah login
Route::middleware(['auth'])->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Pesanan
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/checkout/{product_id}', function($product_id) {
        $product = \App\Models\Product::findOrFail($product_id);
        return view('orders.create', compact('product'));
    })->name('orders.create');

    // Keranjang
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Pembayaran & Sukses
    Route::get('/orders/{order}/payment', function(\App\Models\Order $order) {
        if ($order->user_id !== auth()->id()) { abort(403); }
        $order->load('orderItems.product', 'payment');
        return view('orders.payment', compact('order'));
    })->name('orders.payment');
    Route::get('/orders/{order}/success', function(\App\Models\Order $order) {
        if ($order->user_id !== auth()->id()) { abort(403); }
        $order->load('orderItems.product', 'payment');
        return view('orders.success', compact('order'));
    })->name('orders.success');
    Route::post('/orders/{order}/upload-proof', [OrderController::class, 'uploadProof'])->name('orders.uploadProof');
});

// Routes khusus Admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AdminController::class, 'orderDetail'])->name('orders.detail');
    Route::patch('/orders/{order}/status/{status}', [AdminController::class, 'updateOrderStatus'])->name('orders.status');

    // CRUD Produk (admin)
    Route::resource('products', ProductController::class)->except(['index', 'show']);
    Route::get('/products-list', [AdminController::class, 'adminProducts'])->name('products.index');

    // Kategori
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');

    // Pelanggan
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers');

    // Analisis
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');

    // Keuangan
    Route::get('/finance', [AdminController::class, 'finance'])->name('finance');

    // Pengaturan
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});
