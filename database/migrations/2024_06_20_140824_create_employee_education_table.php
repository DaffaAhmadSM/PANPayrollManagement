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
        Schema::create('employee_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('education_level_id')->constrained('education_levels');
            $table->string('institution')->default('N/A');
            $table->decimal('grade_point_avg', 8, 2)->default(0);
            $table->decimal('grade_point_scale', 8 ,2)->default(0);
            $table->integer('from_year')->default(0);
            $table->integer('to_year')->default(0);
            $table->text('notes')->default('N/A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_education');
    }
};
