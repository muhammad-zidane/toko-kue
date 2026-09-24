<?php

use App\Models\CustomizationOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
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
    $this->assertDatabaseCount('order_items', 1);
    $this->assertDatabaseCount('payments', 1);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 9]);
});

it('rejects an unavailable product during checkout without changing order data or stock', function () {
    $user    = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => false]);

    $response = $this->actingAs($user)->post('/orders', validCheckoutData($product));

    $response->assertSessionHasErrors(['availability' => "Produk {$product->name} tidak tersedia."]);
    expect(Order::count())->toBe(0);
    expect(OrderItem::count())->toBe(0);
    expect(Payment::count())->toBe(0);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
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

it('rejects duplicate product items when their total quantity exceeds stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 5, 'is_available' => true]);
    $data = validCheckoutData($product);
    $data['items'] = [
        ['product_id' => $product->id, 'quantity' => 4],
        ['product_id' => $product->id, 'quantity' => 4],
    ];

    $response = $this->actingAs($user)->post('/orders', $data);

    $response->assertSessionHasErrors('stock');
    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('payments', 0);
    $this->assertDatabaseCount('order_items', 0);
    expect($product->fresh()->stock)->toBe(5);
});

it('keeps duplicate product items with different customizations separate when stock is sufficient', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);
    $firstOption = CustomizationOption::create([
        'type' => 'rasa',
        'name' => 'Coklat',
        'extra_price' => 1000,
    ]);
    $secondOption = CustomizationOption::create([
        'type' => 'rasa',
        'name' => 'Vanila',
        'extra_price' => 2000,
    ]);
    $data = validCheckoutData($product);
    $data['items'] = [
        ['product_id' => $product->id, 'quantity' => 4, 'customizations' => json_encode([$firstOption->id])],
        ['product_id' => $product->id, 'quantity' => 4, 'customizations' => json_encode([$secondOption->id])],
    ];

    $response = $this->actingAs($user)->post('/orders', $data);

    $response->assertSessionHasNoErrors()->assertRedirect();
    $order = Order::with('orderItems.customizations')->sole();
    expect($order->orderItems)->toHaveCount(2);
    expect($order->orderItems->pluck('quantity')->all())->toBe([4, 4]);
    expect($order->orderItems->map(fn ($item) => $item->customizations->sole()->customization_option_id)->all())
        ->toBe([$firstOption->id, $secondOption->id]);
    $this->assertDatabaseCount('payments', 1);
    expect($product->fresh()->stock)->toBe(2);
});
