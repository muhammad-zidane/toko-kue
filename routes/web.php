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
    return view('home.index', compact('categories', 'featuredProducts'));
})->name('home');

// Produk (publik)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Auth routes (dari Breeze)
require __DIR__.'/auth.php';

// Routes untuk user yang sudah login
Route::middleware(['auth'])->group(function () {

    // Profile (dari Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Pesanan
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/create/{product}', function(\App\Models\Product $product) {
        return view('orders.create', compact('product'));
    })->name('orders.create');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart/checkout/{id}', function($id) {
    $product = \App\Models\Product::findOrFail($id);
    return view('orders.create', compact('product'));
    })->name('cart.checkout');
    Route::get('/orders/{order}/payment', function(\App\Models\Order $order) {
    $order->load('orderItems.product');
    return view('orders.payment', compact('order'));
    })->name('orders.payment');
    Route::get('/orders/{order}/success', function(\App\Models\Order $order) {
    $order->load('orderItems.product');
    return view('orders.success', compact('order'));
    })->name('orders.success');
});

// Routes khusus Admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::patch('/orders/{order}/status/{status}', [AdminController::class, 'updateOrderStatus'])->name('orders.status');

    // CRUD Produk (admin)
    Route::resource('products', ProductController::class)->except(['index', 'show']);
});
