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
        Schema::create('payroll_pph_end_of_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_employee_id')->constrained('payroll_period_employees');
            $table->decimal('income_employee', 8, 2)->default(0);
            $table->decimal('income_employee_taxable', 8, 2)->default(0);
            $table->decimal('income_company', 8, 2)->default(0);
            $table->decimal('income_company_taxable', 8, 2)->default(0);
            $table->decimal('tax_allowance', 8, 2)->default(0);
            $table->decimal('gross_taxable', 8, 2)->default(0);
            $table->decimal('total_deduction', 8, 2)->default(0);
            $table->decimal('position_expenses', 8, 2)->default(0);
            $table->decimal('jp_employee', 8, 2)->default(0);
            $table->decimal('jht_employee', 8, 2)->default(0);
            $table->decimal('net_income', 8, 2)->default(0);
            $table->decimal('ptkp', 8, 2)->default(0);
            $table->decimal('pkp', 8, 2)->default(0);
            $table->unsignedBigInteger('ter_id')->default(0);
            $table->foreign('ter_id')->references('id')->on('ter_pph21s');
            $table->decimal('ter_pct', 5, 2)->default(0);
            $table->decimal('annual_income_tax', 8, 2)->default(0);
            $table->decimal('deducted_income_tax', 8, 2)->default(0);
            $table->decimal('income_tax', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_pph_end_of_years');
    }
};
