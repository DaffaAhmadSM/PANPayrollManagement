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
        Schema::create('customer_timesheet_overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_timesheet_id')->constrained('customer_timesheets');
            $table->foreignId('multipication_calculator_id')->constrained('multiplication_calculations');
            $table->decimal('hours', 8, 2);
            $table->decimal('total_hours', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_timesheet_overtimes');
    }
};
