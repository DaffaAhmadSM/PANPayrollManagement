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
            $table->foreignId('timesheet_line_id')->constrained('time_sheet_lines');
            $table->foreignId('multiplication_code_id')->constrained('multiplication_calculations');
            $table->decimal('multiplication', 8, 2)->default(0); // init from Multiplication Code
            $table->decimal('hours', 8, 2)->default(0);
            $table->decimal('total_hours', 8, 2)->default(0); // Multiplication * Hours
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
