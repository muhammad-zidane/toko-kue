<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            // Aktif & bisa dipakai
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
                'code'         => 'WELCOME15',
                'type'         => 'percent',
                'value'        => 15,
                'min_purchase' => 150000,
                'usage_limit'  => 100,
                'used_count'   => 0,
                'is_active'    => true,
                'expires_at'   => Carbon::now()->addMonths(6),
            ],
            [
                'code'         => 'FLAT30K',
                'type'         => 'fixed',
                'value'        => 30000,
                'min_purchase' => 200000,
                'usage_limit'  => 30,
                'used_count'   => 0,
                'is_active'    => true,
                'expires_at'   => Carbon::now()->addMonths(2),
            ],
            // Sudah kadaluarsa
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
            // Kuota habis
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

        Voucher::upsert(
            $vouchers,
            ['code'],
            ['type', 'value', 'min_purchase', 'usage_limit', 'used_count', 'is_active', 'expires_at']
        );
    }
}
