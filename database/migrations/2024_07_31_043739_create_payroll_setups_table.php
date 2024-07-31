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
        Schema::create('payroll_setups', function (Blueprint $table) {
            $table->id();
            $table->decimal('meal_allowance', 8, 2)->default(0);
            $table->decimal('transport_allowance', 8, 2)->default(0);
            $table->decimal('bonus', 5, 2)->default(0); // Percentage
            $table->decimal('working_hours_per_month', 8, 2)->default(0);
            $table->decimal('working_days_per_month', 8, 2)->default(0);
            $table->decimal('jht_employee', 5, 2)->default(0); // Percentage (Jaminan Hari Tua)
            $table->decimal('jp_employee', 5, 2)->default(0); // Percentage (Jaminan Pensiun)
            $table->decimal('jp_employee_limit_amount', 8, 2)->default(0);
            $table->decimal('jpk_employee', 5, 2)->default(0); // Percentage (BPJS Kesehatan)
            $table->decimal('jpk_employee_limit_amount', 8, 2)->default(0);
            $table->decimal('jht_company', 5, 2)->default(0); // Percentage
            $table->decimal('jp_company', 5, 2)->default(0); // Percentage
            $table->decimal('jp_company_limit_amount', 8, 2)->default(0);
            $table->decimal('jpk_company', 5, 2)->default(0); // Percentage
            $table->decimal('jpk_company_limit_amount', 8, 2)->default(0);
            $table->decimal('jkk', 5, 2)->default(0); // Percentage
            $table->decimal('jkm', 5, 2)->default(0); // Percentage
            $table->decimal('position_expenses', 5, 2)->default(0); // Percentage
            $table->decimal('position_expenses_limit_amount', 8, 2)->default(0);            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_setups');
    }
};
