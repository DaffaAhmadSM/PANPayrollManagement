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
        Schema::create('number_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->text('description');
            $table->enum('manual', ['Y', 'N'])->default('N');
            $table->integer('starting_number')->default(1);
            $table->integer('ending_number')->default(999999);
            $table->integer('current_number')->default(1);
            $table->date('last_date_used')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_sequences');
    }
};
