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
        Schema::create('employee_rate_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_rate_id')->constrained()->cascadeOnDelete();
            $table->string('emp_id')->index();
            $table->string('classification')->nullable();
            $table->decimal('rate', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_rate_details');
    }
};
