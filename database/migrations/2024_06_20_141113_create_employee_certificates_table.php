<?php

use Carbon\Carbon;
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
        Schema::create('employee_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('certificate_type_id')->constrained('certificate_types');
            $table->string('description')->default('N/A');
            $table->enum('required_renewal', ['yes', 'no'])->default('no');
            $table->string('certificate_number')->default('N/A');
            $table->date('issued_date')->default(Carbon::parse('3000-06-20'));
            $table->string('issued_by')->default('N/A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_certificates');
    }
};
