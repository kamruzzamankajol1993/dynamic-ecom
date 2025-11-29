<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_review_images', function (Blueprint $table) {
            // boolean: 0 = User upload, 1 = Admin upload. Default is 0.
            $table->boolean('is_upload_from_admin')->default(0)->after('image_path');
        });
    }

    public function down()
    {
        Schema::table('product_review_images', function (Blueprint $table) {
            $table->dropColumn('is_upload_from_admin');
        });
    }
};