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
        Schema::create('payroll_employee_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('payroll_component_id')->constrained('payroll_components');
            $table->string('name')->default('N/A');
            $table->string('description')->default('N/A');
            $table->date('start_date')->default(Carbon::parse('3000-06-20'));
            $table->date('end_date')->default(Carbon::parse('3000-06-20'));
            $table->decimal('amount', 8, 2)->default(0);
            $table->string('remark')->default('N/A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_employee_components');
    }
};
