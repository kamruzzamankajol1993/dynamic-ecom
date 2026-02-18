<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        // নতুন দুটি কলাম যোগ করা হচ্ছে যা সবসময় nullable থাকবে
        $table->string('custom_name')->nullable()->after('notes');
        $table->string('custom_number')->nullable()->after('custom_name');
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['custom_name', 'custom_number']);
    });
}
};
