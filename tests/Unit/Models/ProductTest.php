<?php

use App\Models\Category;
use App\Models\Product;

it('can create a product', function () {
    $product = Product::factory()->create();

    expect($product)->toBeInstanceOf(Product::class);
    expect($product->id)->not->toBeNull();
});

it('has correct fillable fields', function () {
    $product = new Product();
    $expected = ['category_id', 'name', 'slug', 'description', 'price', 'stock', 'image', 'is_available', 'badge'];

    expect($product->getFillable())->toBe($expected);
});

it('detects out of stock when stock is zero', function () {
    $product = Product::factory()->outOfStock()->create();

    expect($product->stock)->toBe(0);
});

it('detects available when stock is greater than zero', function () {
    $product = Product::factory()->create(['stock' => 10]);

    expect($product->stock)->toBeGreaterThan(0);
    expect($product->is_available)->toBeTrue();
});

it('belongs to a category', function () {
    $category = Category::factory()->create();
    $product  = Product::factory()->create(['category_id' => $category->id]);

    expect($product->category)->toBeInstanceOf(Category::class);
    expect($product->category->id)->toBe($category->id);
});
