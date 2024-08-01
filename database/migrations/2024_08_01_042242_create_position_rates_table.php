<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('position_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions');
            $table->date('from_date')->default(Carbon::parse('3000-06-20'));
            $table->date('to_date')->default(Carbon::parse('3000-06-20'));
            $table->enum('type', ['hourly', 'daily'])->default('daily');
            $table->decimal('rate', 8, 2)->default(0);
            $table->decimal('meal_per_day', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_rates');
    }
};
