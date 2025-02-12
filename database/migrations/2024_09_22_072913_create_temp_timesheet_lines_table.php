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
        Schema::create('temp_timesheet_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temp_timesheet_id')->constrained('temp_time_sheets')->onDelete('cascade');
            // $table->foreignId('employee_id')->constrained('employees');
            $table->string('no');
            $table->foreignId('working_hours_id')->constrained('working_hours'); // init from Employee
            $table->string('Kronos_job_number')->default('N/A');
            $table->string('parent_id')->default('N/A')->index();
            $table->string('oracle_job_number')->default('N/A')->index();
            // $table->foreignId('position_id')->constrained('positions');
            $table->date('date')->default(Carbon::parse('3000-06-20'))->index();
            $table->decimal('basic_hours', 8, 2)->default(0); // init from Working Hours
            $table->decimal('actual_hours', 8, 2)->default(0);
            $table->decimal('deduction_hours', 8, 2)->default(0); // if Actual Hours < Basic Hours
            $table->decimal('overtime_hours', 8, 2)->default(0); // Actual Hours - Basic Hours
            $table->decimal('total_overtime_hours', 8, 2)->default(0); // Total all "Total Hours" from Timesheet Overtime for current record
            $table->decimal('paid_hours', 8, 2)->default(0); // Basic Hours + Total Overtime Hours
            $table->enum('meal_allowance', ['no', 'yes'])->default('no'); // based on "Actual Hours" compared to "Meal Allowance Setup"
            $table->enum('transport_allowance', ['no', 'yes'])->default('no'); // based on "Actual Hours" compared to "Transport Allowance Setup"
            // $table->foreignId('leave_category_id')->constrained('leave_categories');
            $table->enum('paid_leave', ['no', 'yes'])->default('no'); // init from Leave Category
            $table->string('custom_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_timesheet_lines');
    }
};
