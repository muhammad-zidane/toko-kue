<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RbacSecurityTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function customer_cannot_access_admin_dashboard()
    {
        $customer = User::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
        ]);
        $customer->role = 'customer';
        $customer->save();

        $response = $this->actingAs($customer)->get('/admin/dashboard');

        $this->assertEquals(403, $response->status(), 'Customer should NOT access admin dashboard');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function customer_cannot_access_other_customer_order()
    {
        $customer1 = User::create([
            'name' => 'Customer 1',
            'email' => 'customer1@test.com',
            'password' => bcrypt('password'),
        ]);
        $customer1->role = 'customer';
        $customer1->save();

        $customer2 = User::create([
            'name' => 'Customer 2',
            'email' => 'customer2@test.com',
            'password' => bcrypt('password'),
        ]);
        $customer2->role = 'customer';
        $customer2->save();

        $order1 = Order::create([
            'user_id' => $customer1->id,
            'order_code' => 'ORD-TEST1',
            'status' => 'completed',
            'total_price' => 100000,
            'payment_status' => 'paid',
            'shipping_address' => 'Test Address 1'
        ]);

        $order2 = Order::create([
            'user_id' => $customer2->id,
            'order_code' => 'ORD-TEST2',
            'status' => 'completed',
            'total_price' => 200000,
            'payment_status' => 'paid',
            'shipping_address' => 'Test Address 2'
        ]);

        $response = $this->actingAs($customer1)->get("/orders/{$order2->id}");

        $this->assertEquals(403, $response->status(), 'Customer should NOT access other customer order');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_access_admin_dashboard()
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->role = 'admin';
        $admin->save();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $this->assertEquals(200, $response->status(), 'Admin should access admin dashboard');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_cannot_access_admin()
    {
        $response = $this->get('/admin/dashboard');

        $this->assertThat(
            $response->status(),
            $this->logicalOr(
                $this->equalTo(302),
                $this->equalTo(403)
            ),
            'Unauthenticated should be denied'
        );
    }
}
