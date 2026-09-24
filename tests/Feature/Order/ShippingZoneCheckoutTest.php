<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingZone;
use App\Models\User;

function shippingZoneCheckoutData(Product $product, array $overrides = []): array
{
    return array_replace([
        'delivery_method' => 'delivery',
        'shipping_address' => 'Jl. Mawar No. 10, Jakarta',
        'delivery_date' => now()->addDays(3)->format('Y-m-d'),
        'payment_method' => 'transfer',
        'items' => [['product_id' => $product->id, 'quantity' => 2]],
    ], $overrides);
}

it('rejects an unavailable shipping zone before creating checkout records or changing stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);
    $zone = ShippingZone::create([
        'area_name' => 'Kota Tidak Tersedia',
        'cost' => 17000,
        'is_available' => false,
    ]);

    $response = $this->actingAs($user)->post('/orders', shippingZoneCheckoutData($product, [
        'shipping_zone_id' => $zone->id,
    ]));

    $response->assertRedirect()->assertSessionHasErrors(['shipping_zone_id']);
    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('payments', 0);
    $this->assertDatabaseCount('order_items', 0);
    expect($product->fresh()->stock)->toBe(10);
});

it('uses the database shipping cost for an available delivery zone', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 50000, 'stock' => 10, 'is_available' => true]);
    $zone = ShippingZone::create([
        'area_name' => 'Kota Tersedia',
        'cost' => 17000,
        'is_available' => true,
    ]);

    $response = $this->actingAs($user)->post('/orders', shippingZoneCheckoutData($product, [
        'shipping_zone_id' => $zone->id,
        'shipping_cost' => 1,
    ]));

    $order = Order::sole();
    $response->assertRedirect(route('orders.payment', $order));
    expect($order->delivery_method)->toBe('delivery')
        ->and($order->shipping_address)->toBe('Jl. Mawar No. 10, Jakarta')
        ->and((float) $order->shipping_cost)->toBe(17000.0)
        ->and((float) $order->total_price)->toBe(117000.0)
        ->and($product->fresh()->stock)->toBe(8);
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
    $this->assertDatabaseHas('payments', ['order_id' => $order->id]);
});

it('still checks out pickup without a shipping zone', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);

    $response = $this->actingAs($user)->post('/orders', shippingZoneCheckoutData($product, [
        'delivery_method' => 'pickup',
    ]));

    $order = Order::sole();
    $response->assertRedirect(route('orders.payment', $order));
    expect($order->delivery_method)->toBe('pickup')
        ->and($order->shipping_address)->toBe('Ambil di Toko')
        ->and((float) $order->shipping_cost)->toBe(0.0)
        ->and($product->fresh()->stock)->toBe(8);
    $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $product->id]);
    $this->assertDatabaseHas('payments', ['order_id' => $order->id]);
});
