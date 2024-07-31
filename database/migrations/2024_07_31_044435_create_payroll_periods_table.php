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
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->default('N/A');
            $table->string('description')->default('N/A');
            $table->date('start_date')->default(Carbon::parse('3000-06-20'));
            $table->date('end_date')->default(Carbon::parse('3000-06-20'));
            $table->integer('year')->default(0);
            $table->enum('tax_month', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'])->default('1');
            $table->foreignId('payroll_type_id')->constrained('payroll_types');
            $table->enum('monthly_salary', ['no', 'yes'])->default('no'); // init from Payroll Type
            $table->enum('thr', ['no', 'yes'])->default('no'); // init from Payroll Type
            $table->date('thr_date')->nullable()->default(null); // Enable if THR = yes
            $table->string('remark')->default('N/A');
            $table->enum('locked', ['no', 'yes'])->default('no');
            $table->enum('released', ['no', 'yes'])->default('no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
