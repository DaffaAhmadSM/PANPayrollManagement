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
        Schema::create('temp_time_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('from_date');
            $table->date('to_date');
            $table->longText('description');
            $table->string('filename');
            $table->string('status')->default('draft');
            $table->string('notes')->default('N/A');
            $table->string('random_string')->index();
            $table->foreignId('customer_id')->constrained('customers');
            $table->string("customer_file_name");
            $table->string("employee_file_name");
            $table->integer("customer_total_imported")->default(0);
            $table->integer("employee_total_imported")->default(0);
            $table->double("eti_bonus_percentage", 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_time_sheets');
    }
};
