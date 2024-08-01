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
        Schema::create('position_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions');
            $table->string('name')->default('N/A'); // init from position
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('employee_name')->default('N/A'); // init from employee
            $table->date('assignment_start_date')->default(Carbon::parse('3000-06-20'));
            $table->date('assignment_end_date')->default(Carbon::parse('3000-06-20'));
            $table->enum('main_position', ['no', 'yes'])->default('no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_assignments');
    }
};
