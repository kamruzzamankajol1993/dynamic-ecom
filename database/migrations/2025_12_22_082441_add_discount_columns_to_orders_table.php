<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // discount_type: 'fixed' অথবা 'percent', ডিফল্ট 'fixed'
            $table->string('discount_type')->default('fixed')->after('subtotal');
            // discount_value: এখানে পার্সেন্টেজ ভ্যালু (যেমন 10) অথবা ফিক্সড অ্যামাউন্ট বসবে
            $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value']);
        });
    }
};