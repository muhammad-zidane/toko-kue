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
        Schema::create('customization_options', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['rasa', 'ukuran', 'topping', 'lainnya'])->default('lainnya');
            $table->string('name');               // e.g. "Coklat", "20cm", "Sprinkles"
            $table->decimal('extra_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customization_options');
        Schema::dropIfExists('order_item_customizations');
    }
};
