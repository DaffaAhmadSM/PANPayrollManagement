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
        Schema::create('leave_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('leave_category_id')->constrained('leave_categories')->onDelete('cascade');
            $table->string('name'); // from employee
            $table->date('date')->default(Carbon::parse('3000-06-20'));
            $table->enum('trans_type', ['Leave', 'Adjustment', 'Entitle'])->default('Entitle');
            $table->string('ref_no')->default('N/A');
            $table->enum('deduct_type', ['yes', 'no', 'none'])->default('none'); // from leave_categories
            $table->enum('paid', ['yes', 'no', 'none'])->default('none'); // from leave_categories
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('from_date_time')->default(Carbon::parse('3000-06-20'));
            $table->date('to_date_time')->default(Carbon::parse('3000-06-20'));
            $table->string('remark')->default('N/A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_histories');
    }
};
