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
        Schema::create('ter_pph21ptkps', function (Blueprint $table) {
            $table->id();
            $table->string('ter')->default('N/A');
            $table->foreignId('ptkp')->constrained('classification_of_tax_payers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ter_pph21ptkps');
    }
};
