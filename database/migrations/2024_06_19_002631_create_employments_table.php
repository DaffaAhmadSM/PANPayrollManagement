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
        Schema::create('employments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('name');
            $table->string('status');
            $table->foreignId('employment_type_id')->constrained('employment_types');
            $table->text('description')->nullable();
            $table->date('from_date');
            $table->date('to_date')->nullable();
            $table->integer('duration')->nullable();
            $table->date('last_date_worked')->nullable();
            $table->enum('terminated', ['yes', 'no'])->nullable();
            $table->date('termination_date')->nullable();
            $table->text('termination_reason')->nullable();
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
