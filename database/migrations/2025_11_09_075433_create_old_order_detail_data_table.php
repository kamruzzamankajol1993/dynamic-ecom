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
        Schema::create('old_order_detail_data', function (Blueprint $table) {
            $table->id();
            $table->string('old_detail_id')->nullable()->unique(); // To store original detail ID
            
            // === Columns from your OrderDetail model ===
            $table->string('order_id')->nullable(); // This will store the OLD invoice ID
            $table->string('product_id')->nullable(); // This will store the OLD product ID
            $table->string('product_variant_id')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('quantity')->default('0');
            $table->string('unit_price')->default('0');
            $table->string('subtotal')->default('0');
            $table->string('discount')->default('0');
            $table->string('after_discount_price')->default('0');
            $table->string('delivery_status')->nullable();

            // === Extra columns as requested ===
            $table->string('product_name')->nullable();
            $table->string('sku')->nullable(); // From the API's 'model_number'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('old_order_detail_data');
    }
};
