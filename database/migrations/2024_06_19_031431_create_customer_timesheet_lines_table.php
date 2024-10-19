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
        Schema::create('customer_timesheet_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_timesheet_id')->constrained('customer_timesheets');
            // $table->foreignId('position_id')->constrained('positions');
            // employee
            // $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('working_hours_id')->constrained('working_hours');
            $table->date('date');
            $table->string('Kronos_job_number');
            $table->string('parent_id');
            $table->string('oracle_job_number');
            $table->decimal('actual_hours', 8, 2);
            $table->decimal('basic_hours', 8, 2);
            $table->decimal('total_overtime_hours', 8, 2);
            $table->decimal('deduction_hours', 8, 2);
            $table->decimal('paid_hours', 8, 2);
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('invoiced', ['yes', 'no'])->default('no');
            $table->string('customer_invoice_id')->default('N/A');
            $table->string('custom_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_timesheet_lines');
    }
};
