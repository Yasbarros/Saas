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
        Schema::create('cashier_sessions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->dateTime('opened_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->decimal('initial_balance', 15, 2);
            $table->decimal('final_balance', 15, 2)->nullable();
            $table->foreignId('user_id_opened')->constrained('users');
            $table->foreignId('user_id_closed')->nullable()->constrained('users');
            $table->string('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
