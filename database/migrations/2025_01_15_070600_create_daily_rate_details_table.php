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
        Schema::create('daily_rate_details', function (Blueprint $table) {
            $table->id();
            $table->string('daily_rate_string')->index();
            // make foreign key from daily_rate_string
            $table->foreign('daily_rate_string')->references('string_id')->on('daily_rates')->onDelete('cascade');
            $table->decimal('value', 8,2);
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_rate_details');
    }
};
