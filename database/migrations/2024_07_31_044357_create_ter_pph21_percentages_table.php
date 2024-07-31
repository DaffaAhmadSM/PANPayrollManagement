<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Type\Decimal;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ter_pph21_percentages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ter_pph21_id')->constrained('ter_pph21s');
            $table->Decimal('gross_income_from', 8, 2)->default(0);
            $table->Decimal('gross_income_to', 8, 2)->default(0);
            $table->decimal('percentage', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ter_pph21_percentages');
    }
};
