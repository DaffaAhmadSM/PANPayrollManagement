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
        Schema::table('temp_time_sheet_overtimes', function (Blueprint $table) {
            // $table->foreign('temp_time_sheet_id')->constrained('temp_time_sheets');
            $table->string('random_string')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_time_sheet_overtimes', function (Blueprint $table) {
            // $table->dropForeign(['temp_time_sheet_id']);
            $table->dropColumn('random_string');
        });
    }
};
