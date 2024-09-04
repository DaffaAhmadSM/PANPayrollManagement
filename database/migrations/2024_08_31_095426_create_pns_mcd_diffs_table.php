<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pns_mcd_diffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temp_time_sheet_id')->constrained('temp_time_sheets')->onDelete('cascade');
            $table->string('employee_name');
            $table->date('date');
            $table->jsonb('mcd_ids');
            $table->jsonb('pns_ids');
            $table->integer('mcd_value');
            $table->integer('pns_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pns_mcd_diffs');
    }
};
