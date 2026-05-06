<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['name' => 'Muhammad Zidane', 'role' => 'Software Engineer di Anthropic', 'text' => 'Kuenya enak sesuai dengan gambar'],
            ['name' => 'Feno Zikrillah', 'role' => 'Game Developer di Sony Entertainment', 'text' => 'Bintang 5'],
            ['name' => 'Rafael Tirta Ramadhan', 'role' => 'Software Engineer di Perkebunan Rafael', 'text' => 'Kue nya bikin nagih banget!'],
        ];

        foreach ($testimonials as $t) {
            Testimonial::create($t);
        }
    }
}
