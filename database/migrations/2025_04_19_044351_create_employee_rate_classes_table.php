<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_rate_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_class_id')->constrained('rate_classes')->onDelete('cascade');
            $table->foreignId('rate_class_parent_id')->constrained('rate_class_parents')->onDelete('cascade');
            $table->string('emp_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_rate_classes');
    }
};
