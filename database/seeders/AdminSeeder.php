<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@jagoankue.test')],
            [
                'name'     => env('ADMIN_NAME', 'Admin Jagoan Kue'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'role'     => 'admin',
            ]
        );
    }
}