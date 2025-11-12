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
        Schema::create('paid_budgets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('budget_id')->constrained('budgets')->onDelete('cascade');
            $table->foreignId('cash_id')->constrained('cash_accounts')->onDelete('cascade');
            $table->decimal('cash', 10, 2);
            $table->decimal('credit_card', 10, 2);
            $table->decimal('debit_card', 10, 2);
            $table->decimal('pix', 10, 2);
            $table->decimal('other', 10, 2);
            $table->unsignedTinyInteger('installments')->default(1)->comment('Número de parcelas');
            $table->foreignId('cashier_session_id')->constrained('cashier_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paid_budgets');
    }
};
