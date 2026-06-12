<?php

use App\Models\Voucher;

it('calculates subtotal correctly', function () {
    $price    = 50000;
    $quantity = 3;

    expect($price * $quantity)->toBe(150000);
});

it('applies percentage discount correctly', function () {
    $price           = 50000;
    $discountPercent = 10;
    $discounted      = $price - ($price * $discountPercent / 100);

    expect($discounted)->toEqual(45000);
});

it('handles zero quantity with zero subtotal', function () {
    $price    = 50000;
    $quantity = 0;

    expect($price * $quantity)->toBe(0);
});

it('calculates voucher fixed discount correctly', function () {
    $voucher = Voucher::factory()->create([
        'type'         => 'fixed',
        'value'        => 20000,
        'min_purchase' => 50000,
        'is_active'    => true,
        'expires_at'   => now()->addDays(7),
        'usage_limit'  => 10,
        'used_count'   => 0,
    ]);

    $discount = $voucher->calculateDiscount(100000);

    expect($discount)->toBe(20000.0);
});

it('calculates voucher percent discount correctly', function () {
    $voucher = Voucher::factory()->create([
        'type'         => 'percent',
        'value'        => 10,
        'min_purchase' => 50000,
        'is_active'    => true,
        'expires_at'   => now()->addDays(7),
        'usage_limit'  => 10,
        'used_count'   => 0,
    ]);

    $discount = $voucher->calculateDiscount(100000);

    expect($discount)->toBe(10000.0);
});
