<?php

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function dpCheckoutData(Product $product, string $method = 'transfer_bank', bool $useDp = true): array
{
    return [
        'delivery_method' => 'pickup',
        'delivery_date' => now()->addDays(3)->format('Y-m-d'),
        'delivery_slot' => '08:00-11:00',
        'payment_method' => $method,
        ...($useDp ? ['use_dp' => 1] : []),
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ];
}

it('settles a DP order in two verified payments while preserving the initial receipt', function () {
    Storage::fake('public');
    $customer = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create(['price' => 300001, 'stock' => 5]);

    $this->actingAs($customer)->post('/orders', dpCheckoutData($product))->assertRedirect();
    $order = Order::with('payment')->sole();
    expect((float) $order->dp_amount)->toBe(150001.0)
        ->and((float) $order->paid_amount)->toBe(0.0)
        ->and($order->payment_status)->toBe('dp')
        ->and((float) $order->payment->amount)->toBe(150001.0);
    $this->get(route('orders.payment', $order))->assertSee('Bayar DP 50%');

    $this->post(route('orders.uploadProof', $order), [
        'proof_image' => UploadedFile::fake()->image('dp.jpg'),
        'amount' => 1,
    ])->assertRedirect(route('orders.success', $order));
    $first = $order->payment->fresh();
    expect($first->status)->toBe('unpaid')
        ->and((float) $order->fresh()->paid_amount)->toBe(0.0);

    $this->actingAs($admin)->post(route('admin.orders.confirmPayment', $order))->assertRedirect();
    $order->refresh()->load('payment');
    expect($first->fresh()->status)->toBe('paid')
        ->and($first->fresh()->paid_at)->not->toBeNull()
        ->and((float) $order->paid_amount)->toBe(150001.0)
        ->and($order->payment_status)->toBe('dp')
        ->and($order->payment->id)->not->toBe($first->id)
        ->and($order->payment->status)->toBe('unpaid')
        ->and((float) $order->payment->amount)->toBe(150000.0)
        ->and($order->payment->paid_at)->toBeNull();

    $this->actingAs($customer)->get(route('orders.index'))->assertSee('Bayar Sisa');
    $this->get(route('orders.show', $order))->assertSee('Bayar Sisa Rp 150.000');
    $this->get(route('orders.payment', $order))->assertSee('Pelunasan Sisa')->assertSee('Rp 150.000');

    $this->post(route('orders.uploadProof', $order), [
        'proof_image' => UploadedFile::fake()->image('remaining.jpg'),
        'amount' => 1,
    ])->assertRedirect(route('orders.success', $order));
    $remaining = $order->payment->fresh();
    expect((float) $remaining->amount)->toBe(150000.0)
        ->and($remaining->status)->toBe('unpaid')
        ->and((float) $order->fresh()->paid_amount)->toBe(150001.0);

    $this->actingAs($admin)->post(route('admin.orders.confirmPayment', $order))->assertRedirect();
    $order->refresh()->load('payment');
    expect($order->payment_status)->toBe('paid')
        ->and((float) $order->paid_amount)->toBe(300001.0)
        ->and($order->payment->status)->toBe('paid')
        ->and($order->payment->paid_at)->not->toBeNull()
        ->and($order->payments()->count())->toBe(2)
        ->and($first->fresh()->proof_image)->not->toBeNull();

    $this->post(route('admin.orders.confirmPayment', $order))->assertSessionHasErrors('error');
    expect((float) $order->fresh()->paid_amount)->toBe(300001.0)
        ->and($order->payments()->count())->toBe(2);
});

it('preserves confirmed DP when remaining proof is rejected or order is completed', function () {
    $customer = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create(['price' => 300000, 'stock' => 5]);
    $this->actingAs($customer)->post('/orders', dpCheckoutData($product))->assertRedirect();
    $order = Order::sole();
    $this->actingAs($admin)->post(route('admin.orders.confirmPayment', $order));
    $remaining = $order->fresh()->payment;

    $this->patch(route('admin.orders.status', [$order, 'completed']))->assertRedirect();
    expect($remaining->fresh()->status)->toBe('unpaid')
        ->and($order->fresh()->payment_status)->toBe('dp');

    $this->post(route('admin.orders.rejectPayment', $order))->assertRedirect();
    expect($order->fresh()->payment_status)->toBe('dp')
        ->and((float) $order->fresh()->paid_amount)->toBe(150000.0)
        ->and((float) $remaining->fresh()->amount)->toBe(150000.0);
});

it('keeps full payment and COD boundaries at one payment record', function (string $method) {
    $customer = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create(['price' => 300000, 'stock' => 5]);
    $this->actingAs($customer)->post('/orders', dpCheckoutData($product, $method, false))->assertRedirect();
    $order = Order::sole();
    expect($order->payment_status)->toBe('unpaid')
        ->and((float) $order->payment->amount)->toBe(300000.0);

    if ($method === 'cod') {
        $this->get(route('orders.payment', $order))->assertRedirect(route('orders.success', $order));
        $this->actingAs($admin)->patch(route('admin.orders.status', [$order, 'completed']))->assertRedirect();
    } else {
        $this->actingAs($admin)->post(route('admin.orders.confirmPayment', $order))->assertRedirect();
    }
    expect($order->fresh()->payment_status)->toBe('paid')
        ->and((float) $order->fresh()->paid_amount)->toBe(300000.0)
        ->and($order->payments()->count())->toBe(1);
})->with(['transfer_bank', 'cod']);
