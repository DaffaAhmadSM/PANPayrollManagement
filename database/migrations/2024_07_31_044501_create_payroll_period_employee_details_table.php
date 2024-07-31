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
        Schema::create('payroll_period_employee_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pay_period_empl_id')->constrained('payroll_period_employees');
            $table->foreignId('payroll_period_id')->constrained('payroll_periods');
            $table->foreignId('payroll_component_id')->constrained('payroll_components');
            $table->string('description')->default('N/A'); // init from Component
            $table->enum('category', ['income', 'deduction'])->default('Income'); // init from Component
            $table->enum('type', [
                'Basic Salary',
                'Meal Allowance',
                'Transport Allowance',
                'Housing Allowance',
                'Family Allowance',
                'Overtime',
                'Bonus',
                'Annual Leave',
                'Compensation',
                'THR',
                'Severance',
                'Correction',
                'JHT Employee',
                'JP Employee',
                'JPK Employee',
                'JHT Company',
                'JP Company',
                'JPK Company',
                'JKK',
                'JKM'
            ])->default('Basic Salary'); // init from Component
            $table->enum('calculation_method', ['fixed', 'unfixed'])->default('fixed'); // init from Component
            $table->enum('payment_base', ['employee', 'company'])->default('employee'); // init from Component
            $table->enum('taxable', ['no', 'yes'])->default('no'); // init from Component
            $table->enum('prorate', ['no', 'yes'])->default('no'); // init from Payroll Employee Component
            $table->decimal('basic_calculation', 8, 2)->default(0); // Count working days in period (excl. Holiday)
            $table->decimal('basic_amount', 8, 2)->default(0); // init from Payroll Employee Component
            $table->decimal('deduction_days', 8, 2)->default(0);
            $table->decimal('deduction_amount', 8, 2)->default(0);
            $table->decimal('amount', 8, 2)->default(0);
            $table->string('note')->default('N/A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_period_employee_details');
    }
};
