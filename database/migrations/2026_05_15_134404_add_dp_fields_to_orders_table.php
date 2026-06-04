<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'dp', 'paid'])->default('unpaid')->after('total_price');
            $table->decimal('dp_amount', 10, 2)->default(0)->after('payment_status');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('dp_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'dp_amount', 'paid_amount']);
        });
    }
};
