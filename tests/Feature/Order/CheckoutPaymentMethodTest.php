<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;

function paymentMethodCheckoutData(Product $product, string $method): array
{
    return [
        'delivery_method' => 'pickup',
        'delivery_date' => now()->addDays(3)->format('Y-m-d'),
        'payment_method' => $method,
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ];
}

it('rejects an unsupported payment method before creating checkout records or changing stock', function (string $method) {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);

    $response = $this->actingAs($user)->post('/orders', paymentMethodCheckoutData($product, $method));

    $response->assertSessionHasErrors('payment_method');
    expect(Order::count())->toBe(0)
        ->and(Payment::count())->toBe(0)
        ->and(OrderItem::count())->toBe(0)
        ->and($product->fresh()->stock)->toBe(10);
})->with(['abc', 'transfer']);

it('accepts each supported payment method and persists the validated value', function (string $method) {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);

    $response = $this->actingAs($user)->post('/orders', paymentMethodCheckoutData($product, $method));

    $response->assertSessionHasNoErrors();
    $order = Order::with('payment')->sole();
    $payment = Payment::sole();
    $response->assertRedirect(route($method === 'cod' ? 'orders.success' : 'orders.payment', $order));
    expect($payment->payment_method)->toBe($method)
        ->and($payment->payment_method)->toBeIn(['transfer_bank', 'ewallet', 'qris', 'cod'])
        ->and($order->payment->id)->toBe($payment->id)
        ->and(OrderItem::count())->toBe(1)
        ->and($product->fresh()->stock)->toBe(9);
})->with(['transfer_bank', 'ewallet', 'qris', 'cod']);
