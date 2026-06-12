<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

it('generates unique order codes', function () {
    $order1 = Order::factory()->create();
    $order2 = Order::factory()->create();

    expect($order1->order_code)->not->toBe($order2->order_code);
});

it('has a total price', function () {
    $order = Order::factory()->create(['total_price' => 150000]);

    expect($order->total_price)->toBe(150000);
});

it('belongs to a user', function () {
    $user  = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    expect($order->user)->toBeInstanceOf(User::class);
    expect($order->user->id)->toBe($user->id);
});

it('has many order items', function () {
    $order = Order::factory()->create();
    OrderItem::factory()->count(3)->create(['order_id' => $order->id]);

    expect($order->orderItems)->toBeInstanceOf(Collection::class);
    expect($order->orderItems)->toHaveCount(3);
});

it('has valid status values', function () {
    $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
    $order         = Order::factory()->create(['status' => 'pending']);

    expect(in_array($order->status, $validStatuses))->toBeTrue();
});
