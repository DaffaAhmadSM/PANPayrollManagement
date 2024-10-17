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
        Schema::create('general_setups', function (Blueprint $table) {
            $table->id();
            $table->string("customer")->default("N/A");
            $table->string('customer_contract')->default("N/A");
            $table->string('customer_timesheet')->default("N/A");
            $table->string('customer_invoice')->default("N/A");
            $table->string('employee')->default("N/A");
            $table->string('leave_request')->default("N/A");
            $table->string('leave_adjustment')->default("N/A");
            $table->string('timesheet')->default("N/A");
            $table->string('invent_journal_id')->default("N/A");
            $table->string('invent_trans_id')->default("N/A");
            $table->string('vacancy_no')->default("N/A");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_setups');
    }
};
