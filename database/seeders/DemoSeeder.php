<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $customerModels = $this->createCustomers();
        $this->createAddresses($customerModels);
        $this->ensureExtraProducts();
        $this->createOrders($customerModels);
    }

    private function createCustomers(): array
    {
        $customers = [
            ['name' => 'Customer Demo',  'email' => 'customer@jagoan-kue.test'],
            ['name' => 'Budi Santoso',   'email' => 'budi@mail.test'],
            ['name' => 'Siti Rahayu',    'email' => 'siti@mail.test'],
            ['name' => 'Dian Permata',   'email' => 'dian@mail.test'],
        ];

        $customerModels = [];
        foreach ($customers as $c) {
            $customerModels[] = User::firstOrCreate(
                ['email' => $c['email']],
                ['name' => $c['name'], 'password' => Hash::make('password'), 'role' => 'customer']
            );
        }

        return $customerModels;
    }

    private function createAddresses(array $customerModels): void
    {
        $alamatData = [
            [
                'label'          => 'Rumah',
                'recipient_name' => $customerModels[0]->name,
                'phone'          => '081234567890',
                'street'         => 'Jl. Sudirman No. 10',
                'city'           => 'Padang',
                'is_default'     => true,
            ],
            [
                'label'          => 'Rumah',
                'recipient_name' => $customerModels[1]->name,
                'phone'          => '081234567891',
                'street'         => 'Jl. Ahmad Yani No. 5',
                'city'           => 'Bukittinggi',
                'is_default'     => true,
            ],
            [
                'label'          => 'Kantor',
                'recipient_name' => $customerModels[1]->name,
                'phone'          => '081234567891',
                'street'         => 'Jl. M. Yamin No. 22',
                'city'           => 'Bukittinggi',
                'is_default'     => false,
            ],
            [
                'label'          => 'Rumah',
                'recipient_name' => $customerModels[2]->name,
                'phone'          => '081234567892',
                'street'         => 'Jl. Veteran No. 17',
                'city'           => 'Payakumbuh',
                'is_default'     => true,
            ],
            [
                'label'          => 'Rumah',
                'recipient_name' => $customerModels[3]->name,
                'phone'          => '081234567893',
                'street'         => 'Jl. Diponegoro No. 8',
                'city'           => 'Padang Panjang',
                'is_default'     => true,
            ],
        ];

        $userAddressMap = [0, 1, 1, 2, 3];
        foreach ($alamatData as $i => $alamat) {
            Address::firstOrCreate(
                [
                    'user_id' => $customerModels[$userAddressMap[$i]]->id,
                    'street'  => $alamat['street'],
                ],
                array_merge($alamat, ['user_id' => $customerModels[$userAddressMap[$i]]->id])
            );
        }
    }

    private function ensureExtraProducts(): void
    {
        $categories = Category::pluck('id', 'slug');
        $defaultCatId = $categories->first() ?? 1;

        $extraProducts = [
            ['name' => 'Tart Buah Segar',     'category_slug' => 'kue-ulang-tahun', 'price' => 280000, 'stock' => 8],
            ['name' => 'Cheesecake New York', 'category_slug' => 'kue-ulang-tahun', 'price' => 320000, 'stock' => 6],
            ['name' => 'Opera Cake',          'category_slug' => 'kue-pernikahan',  'price' => 450000, 'stock' => 4],
            ['name' => 'Mille Crepe',         'category_slug' => 'kue-ulang-tahun', 'price' => 380000, 'stock' => 7],
            ['name' => 'Kastengel',           'category_slug' => 'kue-kering',      'price' => 90000,  'stock' => 40],
            ['name' => 'Lidah Kucing',        'category_slug' => 'kue-kering',      'price' => 75000,  'stock' => 40],
        ];

        foreach ($extraProducts as $p) {
            $catId = $categories->get($p['category_slug']) ?? $defaultCatId;
            Product::firstOrCreate(
                ['name' => $p['name']],
                [
                    'slug'         => Str::slug($p['name']),
                    'category_id'  => $catId,
                    'price'        => $p['price'],
                    'stock'        => $p['stock'],
                    'description'  => 'Deskripsi ' . $p['name'],
                    'is_available' => true,
                ]
            );
        }
    }

    private function createOrders(array $customerModels): void
    {
        $products = Product::all();
        if ($products->isEmpty()) {
            return;
        }

        $statuses    = ['pending', 'processing', 'shipped', 'completed', 'completed', 'completed', 'cancelled', 'processing', 'shipped', 'completed'];
        $payStatuses = ['unpaid',  'paid',       'paid',    'paid',      'paid',      'paid',      'unpaid',    'dp',         'paid',    'paid'];

        foreach ($statuses as $idx => $status) {
            $customer        = $customerModels[$idx % count($customerModels)];
            $pickedProducts  = $products->random(min(rand(1, 3), $products->count()));
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
            $createdAt = Carbon::now()->subDays(rand(0, 60));

            $order = Order::create([
                'user_id'          => $customer->id,
                'order_code'       => 'ORD-DEMO-' . strtoupper(Str::random(6)),
                'status'           => $status,
                'shipping_address' => 'Jl. Demo No. ' . ($idx + 1) . ', Padang, Sumatera Barat',
                'total_price'      => $total,
                'shipping_cost'    => $shipping,
                'payment_status'   => $payStatuses[$idx],
                'dp_amount'        => $dpAmt,
                'paid_amount'      => $payStatuses[$idx] === 'paid' ? $total : $dpAmt,
                'delivery_method'  => $shipping > 0 ? 'delivery' : 'pickup',
                'delivery_date'    => Carbon::now()->addDays(rand(2, 14))->format('Y-m-d'),
                'created_at'       => $createdAt,
                'updated_at'       => $createdAt->copy()->addDays(rand(0, 30)),
            ]);

            $itemRows = array_map(fn ($item) => [
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'price'      => $item['price'],
                'quantity'   => $item['quantity'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ], $items);
            OrderItem::insert($itemRows);

            if (in_array($payStatuses[$idx], ['paid', 'dp'])) {
                $payAmount = $payStatuses[$idx] === 'dp' ? $dpAmt : $total;

                Payment::create([
                    'order_id'       => $order->id,
                    'payment_method' => 'transfer_bank',
                    'status'         => 'paid',
                    'amount'         => $payAmount,
                    'proof_image'    => null,
                    'paid_at'        => $createdAt->copy()->addDay(),
                ]);
            }
        }
    }
}
