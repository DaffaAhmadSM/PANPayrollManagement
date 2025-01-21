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
        Schema::create('daily_rates', function (Blueprint $table) {
            $table->id();
            $table->string('temptimesheet_string')->index();
            $table->string('string_id')->index();
            $table->decimal('work_hours_total', 8, 2);
            $table->decimal('invoice_hours_total', 8, 2);
            $table->decimal('amount_total', 18, 6);
            $table->decimal('eti_bonus_total', 18, 6);
            $table->decimal('grand_total',18, 6);
            $table->decimal('rate', 12,2);
            $table->string('employee_name');
            $table->string('leg_id');
            $table->string('classification');
            $table->string('SLO');
            $table->string('kronos_job_number');
            $table->string('parent_id');
            $table->string('oracle_job_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_rates');
    }
};
