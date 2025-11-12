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
    
        Schema::create('price_table_procedures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('price_table_id')->constrained('price_tables')->onDelete('cascade');
            $table->foreignId('procedure_id')->constrained('procedures')->onDelete('cascade');
            $table->decimal('value', 10, 2);
        });
        Schema::create('treatment_procedures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('execution_date');
            $table->foreignId('treatment_id')->constrained('treatments')->onDelete('cascade');
            // $table->foreignId('dentist_id')->constrained('dentists')->onDelete('cascade');
            $table->foreignId('procedure_id')->constrained('procedures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_table_procedures');
        Schema::table('price_table_procedures', function (Blueprint $table) {
            $table->dropForeign(['price_table_id']);
            $table->dropForeign(['procedure_id']);
        });
        Schema::dropIfExists('treatment_procedures');
        Schema::table('treatment_procedures', function (Blueprint $table) {
            $table->dropForeign(['treatment_id']);
            $table->dropForeign(['dentist_id']);
            $table->dropForeign(['procedure_id']);
        });
    }
};
