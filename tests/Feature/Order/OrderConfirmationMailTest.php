<?php

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function orderConfirmationCheckoutData(Product $product, string $paymentMethod): array
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

it('confirms COD without transfer or proof upload instructions', function () {
    Mail::fake();
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 300000, 'stock' => 5]);

    $this->actingAs($user)->post('/orders', orderConfirmationCheckoutData($product, 'cod'))
        ->assertRedirect();

    Mail::assertQueued(OrderConfirmationMail::class);
    $order = Order::with('user', 'payment', 'orderItems.product')->sole();
    $body = (new OrderConfirmationMail($order))->render();

    expect($body)->toContain('Pembayaran COD dilakukan saat pesanan diterima atau diambil.')
        ->not->toContain('Silakan lakukan pembayaran dan upload bukti transfer')
        ->not->toContain('Upload Bukti Pembayaran');
});

it('keeps payment and proof upload instructions for unpaid non-COD orders', function () {
    Mail::fake();
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 300000, 'stock' => 5]);

    $this->actingAs($user)->post('/orders', orderConfirmationCheckoutData($product, 'transfer_bank'))
        ->assertRedirect();

    Mail::assertQueued(OrderConfirmationMail::class);
    $order = Order::with('user', 'payment', 'orderItems.product')->sole();
    $body = (new OrderConfirmationMail($order))->render();

    expect($body)->toContain('Silakan lakukan pembayaran dan upload bukti transfer')
        ->toContain('Upload Bukti Pembayaran')
        ->not->toContain('Pembayaran COD dilakukan');
});
