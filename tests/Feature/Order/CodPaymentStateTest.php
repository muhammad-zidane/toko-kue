<?php

use App\Http\Controllers\AdminController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;

function codPaymentCheckoutData(Product $product, string $paymentMethod = 'cod'): array
{
    return [
        'delivery_method' => 'pickup',
        'delivery_date' => now()->addDays(3)->format('Y-m-d'),
        'payment_method' => $paymentMethod,
        'items' => [
            ['product_id' => $product->id, 'quantity' => 1],
        ],
    ];
}

it('creates a processable COD order without recording received money', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 300000, 'stock' => 5]);

    $response = $this->actingAs($user)->post('/orders', codPaymentCheckoutData($product));

    $order = Order::with('payment', 'orderItems')->sole();
    $response->assertRedirect(route('orders.success', $order));
    expect($order->status)->toBe('processing')
        ->and($order->payment_status)->toBe('unpaid')
        ->and((float) $order->paid_amount)->toBe(0.0)
        ->and($order->payment->payment_method)->toBe('cod')
        ->and($order->payment->status)->toBe('unpaid')
        ->and((float) $order->payment->amount)->toBe(300000.0)
        ->and($order->payment->paid_at)->toBeNull()
        ->and($order->orderItems)->toHaveCount(1);
    expect($product->fresh()->stock)->toBe(4);
});

it('excludes a newly checked out COD order from finance revenue', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 300000, 'stock' => 5]);

    $this->actingAs($user)->post('/orders', codPaymentCheckoutData($product))
        ->assertRedirect();

    $finance = app(AdminController::class)->finance()->getData();
    expect($finance['totalRevenue'])->toBe(0.0)
        ->and($finance['paidCount'])->toBe(0)
        ->and($finance['pendingPayments'])->toBe(300000.0);
});

it('rejects COD with DP before creating any checkout records', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 300000, 'stock' => 5]);
    $data = codPaymentCheckoutData($product);
    $data['use_dp'] = 1;

    $response = $this->actingAs($user)->post('/orders', $data);

    $response->assertSessionHasErrors([
        'use_dp' => 'DP tidak tersedia untuk pembayaran COD.',
    ]);
    expect(Order::count())->toBe(0)
        ->and(Payment::count())->toBe(0)
        ->and(OrderItem::count())->toBe(0)
        ->and($product->fresh()->stock)->toBe(5);
});

it('keeps non-COD checkout payment states unchanged', function (string $method) {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 300000, 'stock' => 5]);

    $response = $this->actingAs($user)->post('/orders', codPaymentCheckoutData($product, $method));

    $order = Order::with('payment')->sole();
    $response->assertRedirect(route('orders.payment', $order));
    expect($order->status)->toBe('pending')
        ->and($order->payment_status)->toBe('unpaid')
        ->and((float) $order->paid_amount)->toBe(0.0)
        ->and($order->payment->payment_method)->toBe($method)
        ->and($order->payment->status)->toBe('unpaid')
        ->and((float) $order->payment->amount)->toBe(300000.0)
        ->and($order->payment->paid_at)->toBeNull();
})->with(['transfer_bank', 'ewallet', 'qris']);

it('keeps non-COD DP checkout behavior unchanged', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 300000, 'stock' => 5]);
    $data = codPaymentCheckoutData($product, 'transfer_bank');
    $data['use_dp'] = 1;

    $this->actingAs($user)->post('/orders', $data)->assertRedirect();

    $order = Order::with('payment')->sole();
    expect($order->status)->toBe('pending')
        ->and($order->payment_status)->toBe('dp')
        ->and((float) $order->dp_amount)->toBe(150000.0)
        ->and((float) $order->paid_amount)->toBe(0.0)
        ->and($order->payment->status)->toBe('unpaid')
        ->and((float) $order->payment->amount)->toBe(150000.0)
        ->and($order->payment->paid_at)->toBeNull();
});
