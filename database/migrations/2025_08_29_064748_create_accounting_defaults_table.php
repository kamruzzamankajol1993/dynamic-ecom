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
        Schema::create('accounting_defaults', function (Blueprint $table) {
            $table->id();
             $table->string('transaction_type')->unique();
        $table->foreignId('debit_account_id')->nullable()->constrained('accounts')->onDelete('set null');
        $table->foreignId('credit_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_defaults');
    }
};
