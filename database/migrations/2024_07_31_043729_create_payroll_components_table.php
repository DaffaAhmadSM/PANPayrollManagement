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
        Schema::create('payroll_components', function (Blueprint $table) {
            $table->id();
            $table->string('code')->default('N/A');
            $table->string('description')->default('N/A');
            $table->enum('category', ['income', 'deduction'])->default('income');
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
            ])->default('Basic Salary');
            $table->enum('calculation_method', ['fixed', 'unfixed'])->default('fixed');
            $table->enum('payment_base', ['employee', 'company'])->default('employee');
            $table->enum('taxable', ['no', 'yes'])->default('no');
            $table->enum('prorate', ['no', 'yes'])->default('no');
            $table->string('remark')->default('N/A');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_components');
    }
};
