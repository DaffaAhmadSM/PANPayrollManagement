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
        Schema::create('payroll_period_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods');
            $table->string('description')->default('N/A'); // init from Payroll Period
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('name')->default('N/A'); // init from Employee
            $table->string('position')->default('N/A'); // init from Employee
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed', 'None'])->default('Single'); // init from Employee
            $table->date('join_date')->default(Carbon::parse('3000-06-20')); // init from Employee
            $table->string('tax_number')->default('N/A'); // init from Employee
            $table->string('classification_of_tax_payer')->default('N/A'); // init from Employee
            $table->string('bpjs_tk_number')->default('N/A'); // init from Employee
            $table->string('bpjs_medical_number')->default('N/A'); // init from Employee
            $table->string('bank_number')->default('N/A'); // init from Employee
            $table->decimal('income_employee', 8, 2)->default(0);
            $table->decimal('income_employee_taxable', 8, 2)->default(0);
            $table->decimal('income_company', 8, 2)->default(0);
            $table->decimal('income_company_taxable', 8, 2)->default(0);
            $table->decimal('tax_allowance', 8, 2)->default(0);
            $table->decimal('gross_taxable', 8, 2)->default(0);
            $table->decimal('total_deduction', 8, 2)->default(0);
            $table->decimal('position_expenses', 8, 2)->default(0); // Tunjangan Jabatan
            $table->unsignedBigInteger('ter_id')->default(0);
            $table->foreign('ter_id')->references('id')->on('ter_pph21s');
            $table->decimal('ter_pct', 5, 2)->default(0);
            $table->decimal('income_tax', 8, 2)->default(0);
            $table->decimal('take_home_pay', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_period_employees');
    }
};
