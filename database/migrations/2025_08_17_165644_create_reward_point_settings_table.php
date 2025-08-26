<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRewardPointSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('reward_point_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(true);
            $table->integer('earn_points_per_unit')->comment('e.g., 1 point');
            $table->decimal('earn_per_unit_amount', 10, 2)->comment('e.g., for every $100 spent');
            $table->integer('redeem_points_per_unit')->comment('e.g., 100 points');
            $table->decimal('redeem_per_unit_amount', 10, 2)->comment('e.g., equals $1 discount');
            $table->timestamps();
        });

        // Insert default settings
        DB::table('reward_point_settings')->insert([
            'is_enabled' => true,
            'earn_points_per_unit' => 1,
            'earn_per_unit_amount' => 100,
            'redeem_points_per_unit' => 100,
            'redeem_per_unit_amount' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('reward_point_settings');
    }
}
