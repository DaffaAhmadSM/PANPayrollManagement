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
        Schema::create('time_sheet_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timesheet_id')->constrained('time_sheets');
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('working_hours_id')->constrained('working_hours'); // init from Employee
            $table->foreignId('position_id')->constrained('positions');
            $table->date('date')->default(Carbon::parse('3000-06-20'));
            $table->decimal('basic_hours', 8, 2)->default(0); // init from Working Hours
            $table->decimal('actual_hours', 8, 2)->default(0);
            $table->decimal('deduction_hours', 8, 2)->default(0); // if Actual Hours < Basic Hours
            $table->decimal('overtime_hours', 8, 2)->default(0); // Actual Hours - Basic Hours
            $table->decimal('total_overtime_hours', 8, 2)->default(0); // Total all "Total Hours" from Timesheet Overtime for current record
            $table->decimal('paid_hours', 8, 2)->default(0); // Basic Hours + Total Overtime Hours
            $table->enum('meal_allowance', ['no', 'yes'])->default('no'); // based on "Actual Hours" compared to "Meal Allowance Setup"
            $table->enum('transport_allowance', ['no', 'yes'])->default('no'); // based on "Actual Hours" compared to "Transport Allowance Setup"
            $table->foreignId('leave_category_id')->constrained('leave_categories');
            $table->enum('paid_leave', ['no', 'yes'])->default('no'); // init from Leave Category
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheet_lines');
    }
};
