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
            ['email' => config('admin.email', 'admin@jagoankue.test')],
            [
                'name'     => config('admin.name', 'Admin Jagoan Kue'),
                'password' => Hash::make(config('admin.password', 'password')),
                'role'     => 'admin',
            ]
        );
    }
}