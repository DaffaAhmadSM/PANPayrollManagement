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
        Schema::create('overtime_multiplication_setups', function (Blueprint $table) {
            $table->id();
            $table->enum('day_type', ['Normal', 'Holiday']);    
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->decimal('from_hours', 8, 2);
            $table->decimal('to_hours', 8, 2);
            $table->foreignId('multiplication_calc_id')->constrained('multiplication_calculations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_multiplication_setups');
    }
};
