<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================================
        // 1. CUSTOMER ACCOUNTS DEMO
        // =====================================================================
        $customers = [
            ['name' => 'Budi Santoso', 'email' => 'budi@mail.test'],
            ['name' => 'Siti Rahayu',  'email' => 'siti@mail.test'],
            ['name' => 'Dian Permata', 'email' => 'dian@mail.test'],
        ];

        $customerModels = [];
        foreach ($customers as $c) {
            $customerModels[] = User::firstOrCreate(
                ['email' => $c['email']],
                ['name' => $c['name'], 'password' => Hash::make('password'), 'role' => 'customer']
            );
        }

        // =====================================================================
        // 2. TAMBAH PRODUK SUPAYA MIN 15
        // =====================================================================
        $extraProducts = [
            ['name' => 'Tart Buah Segar',    'category_id' => 1, 'price' => 280000, 'stock' => 8],
            ['name' => 'Cheesecake New York', 'category_id' => 1, 'price' => 320000, 'stock' => 6],
            ['name' => 'Opera Cake',          'category_id' => 2, 'price' => 450000, 'stock' => 4],
            ['name' => 'Mille Crepe',         'category_id' => 1, 'price' => 380000, 'stock' => 7],
            ['name' => 'Kastengel',           'category_id' => 3, 'price' => 90000,  'stock' => 40],
            ['name' => 'Lidah Kucing',        'category_id' => 3, 'price' => 75000,  'stock' => 40],
        ];

        foreach ($extraProducts as $p) {
            if (!Product::where('name', $p['name'])->exists()) {
                Product::create([
                    'name'         => $p['name'],
                    'slug'         => Str::slug($p['name']),
                    'category_id'  => $p['category_id'],
                    'price'        => $p['price'],
                    'stock'        => $p['stock'],
                    'description'  => 'Deskripsi ' . $p['name'],
                    'is_available' => true,
                ]);
            }
        }

        // =====================================================================
        // 3. VOUCHER DEMO (aktif, expired, habis kuota)
        // =====================================================================
        $vouchers = [
            [
                'code'         => 'DEMO10',
                'type'         => 'percent',
                'value'        => 10,
                'min_purchase' => 100000,
                'usage_limit'  => 50,
                'used_count'   => 0,
                'is_active'    => true,
                'expires_at'   => Carbon::now()->addMonths(3),
            ],
            [
                'code'         => 'EXPIRED25',
                'type'         => 'percent',
                'value'        => 25,
                'min_purchase' => 150000,
                'usage_limit'  => 20,
                'used_count'   => 5,
                'is_active'    => true,
                'expires_at'   => Carbon::now()->subDays(10),
            ],
            [
                'code'         => 'HABIS50K',
                'type'         => 'fixed',
                'value'        => 50000,
                'min_purchase' => 200000,
                'usage_limit'  => 5,
                'used_count'   => 5,
                'is_active'    => true,
                'expires_at'   => Carbon::now()->addMonths(1),
            ],
        ];

        foreach ($vouchers as $v) {
            Voucher::firstOrCreate(['code' => $v['code']], $v);
        }

        // =====================================================================
        // 4. PESANAN DUMMY (10 pesanan bervariasi status)
        // =====================================================================
        $products    = Product::all();
        $statuses    = ['pending', 'processing', 'shipped', 'completed', 'completed', 'completed', 'cancelled', 'processing', 'shipped', 'completed'];
        $payStatuses = ['unpaid',  'paid',       'paid',    'paid',      'paid',      'paid',      'unpaid',    'dp',         'paid',    'paid'];

        foreach ($statuses as $idx => $status) {
            $customer        = $customerModels[$idx % count($customerModels)];
            $pickedProducts  = $products->random(rand(1, 3));
            $subtotal        = 0;
            $items           = [];

            foreach ($pickedProducts as $prod) {
                $qty       = rand(1, 3);
                $subtotal += $prod->price * $qty;
                $items[]   = ['product_id' => $prod->id, 'price' => $prod->price, 'quantity' => $qty];
            }

            $shipping = rand(0, 1) ? 25000 : 0;
            $total    = $subtotal + $shipping;
            $dpAmt    = $payStatuses[$idx] === 'dp' ? (int) round($total * 0.5) : 0;

            $order = Order::create([
                'user_id'         => $customer->id,
                'order_code'      => 'ORD-DEMO-' . strtoupper(Str::random(6)),
                'status'          => $status,
                'shipping_address'=> 'Jl. Demo No. ' . ($idx + 1) . ', Padang, Sumatera Barat',
                'total_price'     => $total,
                'shipping_cost'   => $shipping,
                'payment_status'  => $payStatuses[$idx],
                'dp_amount'       => $dpAmt,
                'paid_amount'     => $payStatuses[$idx] === 'paid' ? $total : $dpAmt,
                'delivery_method' => $shipping > 0 ? 'delivery' : 'pickup',
                'delivery_date'   => Carbon::now()->addDays(rand(2, 14))->format('Y-m-d'),
                'created_at'      => Carbon::now()->subDays(rand(0, 60)),
                'updated_at'      => Carbon::now()->subDays(rand(0, 30)),
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'price'      => $item['price'],
                    'quantity'   => $item['quantity'],
                ]);
            }
        }
    }
}
