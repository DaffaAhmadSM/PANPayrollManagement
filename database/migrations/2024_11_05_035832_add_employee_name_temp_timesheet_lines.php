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
            $table->integer('rate')->default(1);
            
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
            $table->dropColumn('slo_no');
            $table->dropColumn('rate');
        });
    }
};
