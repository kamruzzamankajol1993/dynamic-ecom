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
        Schema::table('products', function (Blueprint $table) {
            // Adding is_pre_order with default false (0)
            // Placing it after 'is_free_delivery' for better organization in DB
            $table->boolean('is_pre_order')->default(0)->after('is_free_delivery'); 
            
            // Adding pre_order_msg as a nullable text field
            $table->text('pre_order_msg')->nullable()->after('is_pre_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_pre_order', 'pre_order_msg']);
        });
    }
};