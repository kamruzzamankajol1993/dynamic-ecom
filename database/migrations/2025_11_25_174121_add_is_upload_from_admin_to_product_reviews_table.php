<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            // boolean: 0 = User, 1 = Admin. Default is 0.
            $table->boolean('is_upload_from_admin')->default(0)->after('is_approved');
        });
    }

    public function down()
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropColumn('is_upload_from_admin');
        });
    }
};