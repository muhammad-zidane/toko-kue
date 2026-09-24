<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;

function deliverySlotCheckoutData(Product $product): array
{
    return [
        'delivery_method' => 'pickup',
        'delivery_date' => now()->addDays(3)->format('Y-m-d'),
        'delivery_slot' => '08:00-11:00',
        'payment_method' => 'transfer_bank',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ];
}

it('rejects missing, empty, unsupported, and overlong delivery slots without checkout side effects', function (string $case) {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);
    $data = deliverySlotCheckoutData($product);

    if ($case === 'missing') {
        unset($data['delivery_slot']);
    } else {
        $data['delivery_slot'] = match ($case) {
            'empty' => '',
            'unsupported' => 'jam bebas',
            'overlong' => str_repeat('x', 256),
        };
    }

    $response = $this->actingAs($user)->post('/orders', $data);

    $response->assertSessionHasErrors('delivery_slot');
    expect(Order::count())->toBe(0)
        ->and(Payment::count())->toBe(0)
        ->and(OrderItem::count())->toBe(0)
        ->and($product->fresh()->stock)->toBe(10);
})->with(['missing', 'empty', 'unsupported', 'overlong']);

it('accepts every official delivery slot and stores the selected value', function (string $slot) {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10, 'is_available' => true]);
    $data = deliverySlotCheckoutData($product);
    $data['delivery_slot'] = $slot;

    $response = $this->actingAs($user)->post('/orders', $data);

    $response->assertSessionHasNoErrors();
    $order = Order::with('payment', 'orderItems')->sole();
    $response->assertRedirect(route('orders.payment', $order));
    expect($order->delivery_slot)->toBe($slot)
        ->and($order->payment->payment_method)->toBe('transfer_bank')
        ->and($order->orderItems)->toHaveCount(1)
        ->and($order->orderItems->sole()->product_id)->toBe($product->id)
        ->and($product->fresh()->stock)->toBe(9);
})->with(['08:00-11:00', '11:00-14:00', '14:00-18:00']);
