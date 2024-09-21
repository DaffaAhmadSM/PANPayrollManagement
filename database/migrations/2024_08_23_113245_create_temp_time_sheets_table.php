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
        Schema::create('temp_time_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('from_date');
            $table->date('to_date');
            $table->longText('description');
            $table->string('filename');
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->string('notes')->default('N/A');
            $table->string('random_string')->index();
            $table->foreignId('customer_id')->constrained('customers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_time_sheets');
    }
};
