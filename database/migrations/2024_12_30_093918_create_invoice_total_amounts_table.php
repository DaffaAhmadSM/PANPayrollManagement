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
        Schema::create('invoice_total_amounts', function (Blueprint $table) {
            $table->id();
            $table->string('oracle_job_number');
            $table->string('random_string')->index();
            $table->decimal('total_amount', 16, 2);
            $table->string('parent_id');
            $table->decimal('total_hours', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_total_amounts');
    }
};
