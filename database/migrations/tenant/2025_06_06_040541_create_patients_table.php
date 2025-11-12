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
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('cnpj');
            $table->string('phone');
            $table->string('status');
            $table->string('opening_hours');
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('postal_code');
            $table->string('street');
            $table->string('number');
            $table->string('complement')->nullable();
            $table->string('neighborhood');
            $table->string('city');
            $table->string('state');
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('social_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone');
            $table->date('date_of_birth');
            $table->string('landline')->nullable();
            $table->string('cpf')->unique()->nullable();
            $table->string('rg')->unique()->nullable();
            $table->string('cpf_legal_guardian')->nullable();
            $table->string('legal_guardian')->nullable();
            $table->string('photo')->nullable();
            $table->enum('sex', ['M', 'F', 'O', 'N'])->comment('M = Masculino, F = Feminino, O = Outro, N = Não Informado')->nullable();
            $table->foreignId('address_id')->onDelete('cascade')->constrained('addresses');
            $table->foreignId('clinic_id')->onDelete('cascade')->constrained('clinics');    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropForeign(['address_id']);
            $table->dropColumn('clinic_id');
            $table->dropColumn('address_id');
        });
    
        Schema::dropIfExists('patients');
        Schema::dropIfExists('clinics');
        Schema::dropIfExists('addresses');
    }
};
