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
        Schema::create('time_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('filename');
            $table->integer('temp_timesheet_id');
            $table->longText('description')->nullable();
            $table->date('from_date')->default(Carbon::parse('3000-06-20'));
            $table->date('to_date')->default(Carbon::parse('3000-06-20'));
            $table->string('status');
            $table->string('notes')->default('N/A');
            $table->string('random_string')->index();
            $table->foreignId('customer_id')->constrained('customers');
            $table->string("customer_file_name");
            $table->string("employee_file_name");
            $table->integer("customer_total_imported")->default(0);
            $table->integer("employee_total_imported")->default(0);
            $table->double("eti_bonus_percentage", 8, 2)->default(0);
            $table->string("rate_id")->nullable();
            $table->string("file_path")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheets');
    }
};
