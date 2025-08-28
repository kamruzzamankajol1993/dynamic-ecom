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
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
                    $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
        $table->foreignId('size_id')->constrained()->onDelete('cascade');
        $table->integer('previous_quantity');
        $table->integer('new_quantity');
        $table->integer('quantity_change');
        $table->string('type')->default('manual_update'); // e.g., manual_update, sale, return
        $table->text('notes')->nullable();
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Tracks which admin made the change

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
