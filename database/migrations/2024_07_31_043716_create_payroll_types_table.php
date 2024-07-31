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
        Schema::create('payroll_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->default('N/A')->unique();
            $table->string('description')->default('N/A');
            $table->enum('monthly_salary', ['yes', 'no'])->default('yes');
            $table->enum('thr', ['yes', 'no'])->default('yes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_types');
    }
};
