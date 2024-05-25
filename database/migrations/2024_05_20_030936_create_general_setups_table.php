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
            $table->foreignId('number_sequence_id')->nullable()->constrained('number_sequences')->onDelete('cascade');
            $table->string("customer");
            $table->string('customer_contract');
            $table->string('customer_timesheet')->nullable();
            $table->string('customer_invoice');
            $table->string('employee')->nullable();
            $table->string('leave_request')->nullable();
            $table->string('leave_adjustment')->nullable();
            $table->string('timesheet')->nullable();
            $table->string('invent_journal_id')->nullable();
            $table->string('invent_trans_id')->nullable();
            $table->string('vacancy_no')->nullable();
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
