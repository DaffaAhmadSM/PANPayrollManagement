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
        Schema::table('temp_timesheet_lines', function (Blueprint $table) {
            $table->string('employee_name')->nullable();
            $table->string('job_dissipline')->nullable();
            $table->string('leg_id')->nullable();
            $table->string('slo_no')->nullable();
            $table->dropColumn('slo_no');
            $table->integer('rate')->default(1);
            $table->string('amount_idr')->nullable();
            $table->string('eti_bonus')->nullable();
            $table->string('amount_total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_timesheet_lines', function (Blueprint $table) {
            $table->dropColumn('employee_name');
            $table->dropColumn('job_dissipline');
            $table->dropColumn('leg_id');
        });
    }
};
