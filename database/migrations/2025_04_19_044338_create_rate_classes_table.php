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
        Schema::create('rate_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_class_parent_id')->constrained('rate_class_parents')->onDelete('cascade');
            $table->string('classification')->index();
            $table->dateTime('from_date')->index();
            $table->dateTime('to_date')->index();
            $table->decimal('rate', 12, 2)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_classes');
    }
};
