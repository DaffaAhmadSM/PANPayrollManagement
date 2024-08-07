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
        Schema::create('customer_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('number_sequence_id')->constrained('number_sequences');
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('code')->default('N/A');
            $table->string('contract_no')->default('N/A');
            $table->string('description')->default('N/A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_contracts');
    }
};
