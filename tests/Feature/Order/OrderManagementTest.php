<?php

use App\Models\Order;
use App\Models\User;

it('user can view own orders', function () {
    $user  = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get('/orders');

    $response->assertStatus(200);
    $response->assertSee($order->order_code);
});

it('user cannot view other user orders detail', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user1->id]);

    $response = $this->actingAs($user2)->get('/orders/' . $order->id);

    $response->assertStatus(403);
});

it('admin can view all orders', function () {
    $admin  = User::factory()->create(['role' => 'admin']);
    $order1 = Order::factory()->create();
    $order2 = Order::factory()->create();

    $response = $this->actingAs($admin)->get('/admin/orders');

    $response->assertStatus(200);
    $response->assertSee($order1->order_code);
    $response->assertSee($order2->order_code);
});

it('admin can update order status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $order = Order::factory()->create(['status' => 'pending']);

    $response = $this->actingAs($admin)->patch('/admin/orders/' . $order->id . '/status/processing');

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'processing']);
});

it('guest cannot access orders list', function () {
    $response = $this->get('/orders');

    $response->assertRedirect('/login');
});
