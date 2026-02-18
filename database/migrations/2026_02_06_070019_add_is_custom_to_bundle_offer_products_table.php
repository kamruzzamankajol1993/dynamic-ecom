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
    Schema::table('bundle_offer_product', function (Blueprint $table) {
        $table->boolean('is_custom')->default(0)->after('get_quantity');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bundle_offer_product', function (Blueprint $table) {
            //
        });
    }
};
