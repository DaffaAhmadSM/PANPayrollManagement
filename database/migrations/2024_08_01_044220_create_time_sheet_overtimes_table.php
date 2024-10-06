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
        Schema::create('time_sheet_overtimes', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->index();
            $table->foreignId('multiplication_id')->constrained('overtime_multiplication_setups');
            $table->string('multiplication_code');
            $table->string('hours');
            $table->string('total_hours');
            $table->string('random_string')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheet_overtimes');
    }
};
