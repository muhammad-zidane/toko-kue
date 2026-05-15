<?php

use App\Models\Category;
use App\Models\Product;

it('shows product catalog page', function () {
    $response = $this->get('/products');

    $response->assertStatus(200);
});

it('displays all available products', function () {
    $product = Product::factory()->create(['is_available' => true]);

    $response = $this->get('/products');

    $response->assertStatus(200);
    $response->assertSee($product->name);
});

it('does not display unavailable products', function () {
    $inactive = Product::factory()->unavailable()->create();

    $response = $this->get('/products');

    $response->assertDontSee($inactive->name);
});

it('can filter products by category', function () {
    $category = Category::factory()->create();
    $product  = Product::factory()->create(['category_id' => $category->id, 'is_available' => true]);

    $response = $this->get('/products?category=' . $category->slug);

    $response->assertStatus(200);
    $response->assertSee($product->name);
});

it('can search products by name', function () {
    $product = Product::factory()->create(['name' => 'Kue Ulang Tahun Spesial', 'is_available' => true]);
    Product::factory()->create(['name' => 'Roti Biasa', 'is_available' => true]);

    $response = $this->get('/products?search=Ulang+Tahun');

    $response->assertStatus(200);
    $response->assertSee('Kue Ulang Tahun Spesial');
});

it('shows product detail page', function () {
    $product = Product::factory()->create(['is_available' => true]);

    $response = $this->get('/products/' . $product->slug);

    $response->assertStatus(200);
    $response->assertSee($product->name);
});

it('returns 404 for nonexistent product', function () {
    $response = $this->get('/products/produk-tidak-ada-xyz');

    $response->assertStatus(404);
});
