<?php

use App\Models\Product;
use App\Models\User;

it('can add product to cart', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5, 'is_available' => true]);

    $this->actingAs($user)->post('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);

    $cart = session('cart');
    expect($cart)->toHaveKey((string) $product->id);
});

it('increments quantity for duplicate product', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);

    $this->actingAs($user)->post('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    $this->actingAs($user)->post('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);

    $cart = session('cart');
    expect($cart[(string) $product->id]['quantity'])->toBe(2);
});

it('can update item quantity', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);

    $this->actingAs($user)->post('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);

    $this->actingAs($user)->post('/cart/update-item', [
        'product_id' => $product->id,
        'quantity'   => 5,
    ]);

    $cart = session('cart');
    expect($cart[(string) $product->id]['quantity'])->toBe(5);
});

it('can remove item from cart', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);

    $this->actingAs($user)->post('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);

    $this->actingAs($user)->post('/cart/remove', ['ids' => [$product->id]]);

    $cart = session('cart', []);
    expect($cart)->not->toHaveKey((string) $product->id);
});

it('calculates cart total correctly', function () {
    $user     = User::factory()->create();
    $product1 = Product::factory()->create(['price' => 50000, 'stock' => 10, 'is_available' => true]);
    $product2 = Product::factory()->create(['price' => 30000, 'stock' => 10, 'is_available' => true]);

    $this->actingAs($user)->post('/cart/add', ['product_id' => $product1->id, 'quantity' => 2]);
    $this->actingAs($user)->post('/cart/add', ['product_id' => $product2->id, 'quantity' => 1]);

    $cart  = session('cart', []);
    $total = 0;
    foreach ($cart as $id => $item) {
        $p = \App\Models\Product::find($id);
        $total += $p->price * $item['quantity'];
    }

    expect($total)->toEqual(130000);
});

it('clears cart after calling clear', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5, 'is_available' => true]);

    $this->actingAs($user)->post('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    $this->actingAs($user)->post('/cart/clear');

    $cart = session('cart', []);
    expect($cart)->toBeEmpty();
});

it('guest is redirected to login when adding to cart', function () {
    $product = Product::factory()->create(['stock' => 5, 'is_available' => true]);

    $response = $this->post('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);

    $response->assertRedirect('/login');
});

it('shows cart page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/cart');

    $response->assertStatus(200);
});
