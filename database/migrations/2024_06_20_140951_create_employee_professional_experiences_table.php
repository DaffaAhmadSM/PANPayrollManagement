<?php

use Carbon\Carbon;
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
        Schema::create('employee_professional_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('employer')->default('N/A');
            $table->string('position')->default('N/A');
            $table->text('homepage')->nullable();
            $table->text('phone')->nullable();
            $table->text('location')->nullable();
            $table->date('start_date')->default(Carbon::parse('3000-06-20'));
            $table->date('end_date')->default(Carbon::parse('3000-06-20'));
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_professional_experiences');
    }
};
