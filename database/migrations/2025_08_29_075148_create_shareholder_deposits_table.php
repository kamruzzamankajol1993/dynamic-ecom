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
        Schema::create('shareholder_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shareholder_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
        $table->decimal('amount', 15, 2);
        $table->date('date');
        $table->text('note')->nullable();
        $table->unsignedBigInteger('cash_account_id'); // For reference
        $table->unsignedBigInteger('equity_account_id'); // For reference
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shareholder_deposits');
    }
};
