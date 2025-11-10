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
        Schema::create('old_order_data', function (Blueprint $table) {
            $table->id();
            // *** THIS IS THE CHANGE ***
            // Stores the ID from your NEW 'customers' table.
            $table->unsignedBigInteger('customer_id')->nullable(); 
            
            // These columns match your Order model
            $table->string('delivery_type')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('subtotal')->default('0');
            $table->string('shipping_cost')->default('0');
            $table->string('discount')->default('0');
            $table->string('total_amount')->default('0');
            $table->string('total_pay')->default('0');
            $table->string('due')->default('0');
            $table->string('cod')->default('0');
            $table->string('old_id')->nullable()->unique(); // To store the original invoice ID
            $table->string('status')->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_term')->nullable();
            $table->string('order_from')->nullable();
            $table->string('trxID')->nullable();
            $table->string('statusMessage')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('old_order_data');
    }
};
