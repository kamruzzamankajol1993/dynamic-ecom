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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
        $table->string('purchase_no')->unique();
        $table->date('purchase_date');
        $table->decimal('subtotal', 10, 2);
        $table->decimal('discount', 10, 2)->default(0.00);
        $table->decimal('shipping_cost', 10, 2)->default(0.00);
        $table->decimal('total_amount', 10, 2);
        $table->decimal('paid_amount', 10, 2)->default(0.00);
        $table->decimal('due_amount', 10, 2)->default(0.00);
        $table->string('payment_status')->default('pending'); // pending, partial, paid
        $table->text('notes')->nullable();
        $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
