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
        Schema::create('temp_pns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temp_time_sheet_id')->constrained('temp_time_sheets');
            $table->string('kronos_job_number');
            $table->string('oracle_job_number');
            $table->string('parent_id');
            $table->string('employee_name');
            $table->string('leg_id');
            $table->string('job_dissipline');
            $table->string('slo_no');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_pns');
    }
};
