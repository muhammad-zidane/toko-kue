<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('delivery_method', ['pickup', 'delivery'])->default('delivery')->after('notes');
            $table->date('delivery_date')->nullable()->after('delivery_method');
            $table->string('delivery_slot')->nullable()->after('delivery_date');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('delivery_slot');
            $table->string('voucher_code')->nullable()->after('shipping_cost');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('voucher_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_method', 'delivery_date', 'delivery_slot', 'shipping_cost', 'voucher_code', 'discount_amount']);
        });
    }
};
