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
        Schema::create('time_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('number_sequence_id')->constrained('number_sequences');
            $table->string('no')->default('N/A'); // init from Number Sequences
            $table->string('description')->default('N/A');
            $table->date('from_date')->default(Carbon::parse('3000-06-20'));
            $table->date('to_date')->default(Carbon::parse('3000-06-20'));
            $table->enum('status', ['Open', 'Posted'])->default('Open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheets');
    }
};
