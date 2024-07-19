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
        Schema::create('employments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('name')->default('N/A');
            $table->enum('status', ['contract', 'permanent','none'])->default('none');
            $table->foreignId('employment_type_id')->constrained('employment_types');
            $table->text('description')->default('N/A');
            $table->enum('permanent', ['yes', 'no'])->nullable();
            $table->date('from_date')->default(Carbon::parse('3000-01-01'));
            $table->date('to_date')->default(Carbon::parse('3000-01-01'));
            $table->integer('duration')->default(1);
            $table->date('last_date_worked')->default(Carbon::parse('3000-01-01'));
            $table->enum('terminated', ['yes', 'no'])->default('no');
            $table->date('termination_date')->default(Carbon::parse('3000-01-01'))->nullable();
            $table->text('termination_reason')->default(Carbon::parse('3000-01-01'))->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employments');
    }
};
