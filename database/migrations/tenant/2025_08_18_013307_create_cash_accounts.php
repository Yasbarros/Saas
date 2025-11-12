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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->decimal('value', 10, 2);
            $table->decimal('unit_value', 10, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->string('status')->nullable();
            $table->boolean('paid')->default(false);
            $table->string('guide')->nullable();
            $table->string('teeth_region')->nullable();
            $table->string('dentition')->nullable();
            $table->foreignId('cash_id')->constrained('cash_accounts')->onDelete('cascade');
            // Campos temporários para armazenar o nome dos dentistas
            $table->string('registration_dentist')->nullable();
            $table->string('procedure_dentist')->nullable();
            $table->foreignId('cashier_session_id')->constrained('cashier_sessions')->onDelete('cascade');

            // FK dentista de registro
            // $table->unsignedBigInteger('registration_dentist_id');
            // $table->foreign('registration_dentist_id', 'fk_procedures_registration_dentist')
            //     ->references('id')->on('dentists')
            //     ->onDelete('cascade');

            // // FK dentista que executa o procedimento
            // $table->unsignedBigInteger('procedure_dentist_id');
            // $table->foreign('procedure_dentist_id', 'fk_procedures_procedure_dentist')
            //     ->references('id')->on('dentists')
            //     ->onDelete('cascade');

            $table->boolean('execute_all_procedures')->default(false);
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            // $table->foreignId('dentist_id')->constrained('dentists')->onDelete('cascade');
            $table->foreignId('price_table_id')->constrained('price_tables')->onDelete('cascade');
            $table->foreignId('procedure_id')->constrained('procedures')->onDelete('cascade');
        });

        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->boolean('status')->default(false);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('cash_accounts');
    }
};
