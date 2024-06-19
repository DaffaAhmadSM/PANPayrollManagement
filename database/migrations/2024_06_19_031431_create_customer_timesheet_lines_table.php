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
            $table->foreignId('position_id')->constrained('positions');
            // employee
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('working_hours_id')->constrained('working_hours');
            $table->date('date');
            $table->string('Kronos_job_number');
            $table->string('parent_id');
            $table->string('oracle_job_number');
            $table->string('service_order');
            $table->decimal('actual_hours', 8, 2);
            $table->decimal('basic_hours', 8, 2);
            $table->decimal('overtime_hours', 8, 2);
            $table->decimal('paid_hours', 8, 2);
            $table->decimal('amount', 10, 2);
            $table->enum('invoiced', ['yes', 'no']);
            $table->foreignId('customer_invoice_id')->constrained('customer_invoices');
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
