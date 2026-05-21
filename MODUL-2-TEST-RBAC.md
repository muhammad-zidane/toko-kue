# MODUL 2 - TEST RBAC & OTORISASI

## Test Plan RBAC Security

Dokumen ini berisi test scenario untuk verify RBAC sudah berfungsi dengan baik.
Semua test harus di-run SETELAH database setup dengan test data.

---

## PREREQUISITE: Setup Test Data

Sebelum jalankan test, pastikan sudah ada test users:

```bash
php artisan tinker
```

Jalankan commands berikut di tinker shell:

```php
// Create admin user
$admin = User::create([
    'name' => 'Admin Test',
    'email' => 'admin@test.local',
    'password' => Hash::make('password123'),
    'role' => 'admin'
]);

// Create customer 1
$user1 = User::create([
    'name' => 'User 1',
    'email' => 'user1@test.local',
    'password' => Hash::make('password123'),
    'role' => 'customer'
]);

// Create customer 2  
$user2 = User::create([
    'name' => 'User 2',
    'email' => 'user2@test.local',
    'password' => Hash::make('password123'),
    'role' => 'customer'
]);

// Create orders for testing
Order::create([
    'user_id' => $user1->id,
    'order_code' => 'ORD-TEST001',
    'status' => 'pending',
    'total_price' => 500000,
    'shipping_address' => 'Jln Test 1',
]);

Order::create([
    'user_id' => $user2->id,
    'order_code' => 'ORD-TEST002',
    'status' => 'pending',
    'total_price' => 600000,
    'shipping_address' => 'Jln Test 2',
]);
```

---

## TEST SCENARIO 1: Regular User Access /admin Routes

### Expected: 403 Forbidden

#### Test 1.1 - Admin Dashboard
```
URL: http://localhost:8000/admin/dashboard
Method: GET
Auth: user1@test.local / password123
Expected Status: 403
Expected Response: Forbidden page or redirect to 403
```

#### Test 1.2 - Admin Orders List
```
URL: http://localhost:8000/admin/orders
Method: GET
Auth: user1@test.local / password123
Expected Status: 403
```

#### Test 1.3 - Admin Customers
```
URL: http://localhost:8000/admin/customers
Method: GET
Auth: user1@test.local / password123
Expected Status: 403
```

---

## TEST SCENARIO 2: User A Accessing User B's Orders

### Expected: 403 Forbidden for all endpoints

#### Test 2.1 - User1 View Order belonging to User2
```
URL: http://localhost:8000/orders/{order_id_of_user2}
Method: GET
Auth: user1@test.local / password123
Expected Status: 403
Expected: Cannot access order belonging to User2
```

#### Test 2.2 - User1 View Order Status of User2
```
URL: http://localhost:8000/orders/{order_id_of_user2}/status
Method: GET
Auth: user1@test.local / password123
Expected Status: 403
```

#### Test 2.3 - User1 View Payment of User2
```
URL: http://localhost:8000/orders/{order_id_of_user2}/payment
Method: GET
Auth: user1@test.local / password123
Expected Status: 403
```

#### Test 2.4 - User1 Upload Proof for User2's Order
```
URL: http://localhost:8000/orders/{order_id_of_user2}/upload-proof
Method: POST
Auth: user1@test.local / password123
Body: proof_image: [image file]
Expected Status: 403
```

#### Test 2.5 - User1 View Invoice of User2
```
URL: http://localhost:8000/orders/{order_id_of_user2}/invoice
Method: GET
Auth: user1@test.local / password123
Expected Status: 403
```

---

## TEST SCENARIO 3: User Can Access Own Orders

### Expected: 200 OK

#### Test 3.1 - User1 View Own Orders List
```
URL: http://localhost:8000/orders
Method: GET
Auth: user1@test.local / password123
Expected Status: 200
Expected: User1 sees ONLY their own orders
Verify: Order list should contain only ORD-TEST001, not ORD-TEST002
```

#### Test 3.2 - User1 View Own Order Detail
```
URL: http://localhost:8000/orders/{order_id_of_user1}
Method: GET
Auth: user1@test.local / password123
Expected Status: 200
Expected: Order detail page loads successfully
```

#### Test 3.3 - User1 View Own Invoice
```
URL: http://localhost:8000/orders/{order_id_of_user1}/invoice
Method: GET
Auth: user1@test.local / password123
Expected Status: 200
Expected: PDF download starts
```

---

## TEST SCENARIO 4: Admin Can Access All Orders

### Expected: 200 OK (can view/manage all)

#### Test 4.1 - Admin View Dashboard
```
URL: http://localhost:8000/admin/dashboard
Method: GET
Auth: admin@test.local / password123
Expected Status: 200
```

#### Test 4.2 - Admin View All Orders
```
URL: http://localhost:8000/admin/orders
Method: GET
Auth: admin@test.local / password123
Expected Status: 200
Expected: Can see orders from all users
```

#### Test 4.3 - Admin View Order Detail (Any User)
```
URL: http://localhost:8000/admin/orders/{order_id_of_user1}
Method: GET
Auth: admin@test.local / password123
Expected Status: 200
```

---

## AUTOMATED TEST SCRIPT

Run these Feature Tests setelah setup test data:

```bash
php artisan test --filter=RbacTest
```

Jika tidak ada test file, buat file di `tests/Feature/RbacTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user1;
    protected User $user2;
    protected Order $order1;
    protected Order $order2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.local',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        $this->user1 = User::create([
            'name' => 'User 1',
            'email' => 'user1@test.local',
            'password' => bcrypt('password123'),
            'role' => 'customer'
        ]);

        $this->user2 = User::create([
            'name' => 'User 2',
            'email' => 'user2@test.local',
            'password' => bcrypt('password123'),
            'role' => 'customer'
        ]);

        $this->order1 = Order::create([
            'user_id' => $this->user1->id,
            'order_code' => 'ORD-001',
            'status' => 'pending',
            'total_price' => 500000,
            'shipping_address' => 'Jln Test 1',
        ]);

        $this->order2 = Order::create([
            'user_id' => $this->user2->id,
            'order_code' => 'ORD-002',
            'status' => 'pending',
            'total_price' => 600000,
            'shipping_address' => 'Jln Test 2',
        ]);
    }

    /** @test */
    public function user_cannot_access_admin_dashboard()
    {
        $this->actingAs($this->user1)
            ->get('/admin/dashboard')
            ->assertStatus(403);
    }

    /** @test */
    public function user_cannot_access_admin_orders()
    {
        $this->actingAs($this->user1)
            ->get('/admin/orders')
            ->assertStatus(403);
    }

    /** @test */
    public function user_cannot_view_other_user_order()
    {
        $this->actingAs($this->user1)
            ->get(route('orders.show', $this->order2))
            ->assertStatus(403);
    }

    /** @test */
    public function user_cannot_access_other_user_order_payment()
    {
        $this->actingAs($this->user1)
            ->get(route('orders.payment', $this->order2))
            ->assertStatus(403);
    }

    /** @test */
    public function user_can_view_own_orders_list()
    {
        $response = $this->actingAs($this->user1)
            ->get(route('orders.index'))
            ->assertStatus(200);

        $response->assertViewHas('orders');
    }

    /** @test */
    public function user_can_view_own_order_detail()
    {
        $this->actingAs($this->user1)
            ->get(route('orders.show', $this->order1))
            ->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_dashboard()
    {
        $this->actingAs($this->admin)
            ->get('/admin/dashboard')
            ->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_orders()
    {
        $this->actingAs($this->admin)
            ->get('/admin/orders')
            ->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_any_order()
    {
        $this->actingAs($this->admin)
            ->get(route('orders.show', $this->order1))
            ->assertStatus(200);
    }
}
```

---

## HOW TO RUN TESTS

### Manual Testing (via Browser)

1. Start dev server:
   ```bash
   php artisan serve
   ```

2. Open browser and navigate to test URLs
3. Login dengan test user credentials
4. Verify responses match expected status codes

### Automated Testing (PHPUnit)

1. Paste test file ke `tests/Feature/RbacTest.php`
2. Run:
   ```bash
   php artisan test tests/Feature/RbacTest.php
   ```

---

## VERIFICATION CHECKLIST

- [ ] Regular user cannot access any /admin/* routes (403)
- [ ] User1 cannot view orders belonging to User2 (403)
- [ ] User1 cannot upload proof for User2's order (403)
- [ ] User1 can view/manage only own orders (200)
- [ ] User1 orders list shows only their own orders
- [ ] Admin can access all admin routes (200)
- [ ] Admin can view orders from any user (200)
- [ ] All order methods have authorization checks

---

## EXPECTED TEST RESULTS

After running all tests:
- **Scenario 1**: ✓ PASS (all /admin returns 403 for regular user)
- **Scenario 2**: ✓ PASS (User A cannot access User B orders - 403)
- **Scenario 3**: ✓ PASS (User can access own orders - 200)
- **Scenario 4**: ✓ PASS (Admin can access all - 200)

---

**Note**: Lapor hasil test setelah menjalankan scenarios di atas.
