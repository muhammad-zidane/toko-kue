<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;

function validCheckoutData(Product $product): array
{
    return [
        'delivery_method' => 'pickup',
        'delivery_date'   => now()->addDays(3)->format('Y-m-d'),
        'payment_method'  => 'transfer',
        'items'           => [
            ['product_id' => $product->id, 'quantity' => 1],
        ],
    ];
}

it('shows checkout page when cart is not empty', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5, 'is_available' => true]);

    $this->actingAs($user)->post('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);

    $response = $this->actingAs($user)->get('/cart/checkout');

    $response->assertStatus(200);
});

it('redirects checkout to cart when cart is empty', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/cart/checkout');

    $response->assertRedirect(route('cart.index'));
});

it('creates order with valid data', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);

    $response = $this->actingAs($user)->post('/orders', validCheckoutData($product));

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
});

it('fails checkout with missing delivery method', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);

    $response = $this->actingAs($user)->post('/orders', [
        'delivery_date'  => now()->addDays(3)->format('Y-m-d'),
        'payment_method' => 'transfer',
        'items'          => [['product_id' => $product->id, 'quantity' => 1]],
    ]);

    $response->assertSessionHasErrors('delivery_method');
});

it('fails checkout with missing delivery date', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);

    $response = $this->actingAs($user)->post('/orders', [
        'delivery_method' => 'pickup',
        'payment_method'  => 'transfer',
        'items'           => [['product_id' => $product->id, 'quantity' => 1]],
    ]);

    $response->assertSessionHasErrors('delivery_date');
});

it('fails checkout with delivery date too soon', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);

    $response = $this->actingAs($user)->post('/orders', [
        'delivery_method' => 'pickup',
        'delivery_date'   => now()->format('Y-m-d'),
        'payment_method'  => 'transfer',
        'items'           => [['product_id' => $product->id, 'quantity' => 1]],
    ]);

    $response->assertSessionHasErrors('delivery_date');
});

it('generates unique order code on checkout', function () {
    $user     = User::factory()->create();
    $product1 = Product::factory()->create(['stock' => 10, 'is_available' => true]);
    $product2 = Product::factory()->create(['stock' => 10, 'is_available' => true]);

    $this->actingAs($user)->post('/orders', validCheckoutData($product1));
    $this->actingAs($user)->post('/orders', validCheckoutData($product2));

    $codes = Order::where('user_id', $user->id)->pluck('order_code');
    expect($codes->unique())->toHaveCount($codes->count());
});

it('guest is redirected to login on checkout', function () {
    $product = Product::factory()->create(['stock' => 5, 'is_available' => true]);

    $response = $this->post('/orders', validCheckoutData($product));

    $response->assertRedirect('/login');
});
