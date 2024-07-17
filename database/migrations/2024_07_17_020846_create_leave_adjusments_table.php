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
        Schema::create('leave_adjusments', function (Blueprint $table) {
            $table->id();
            $table->string('no'); // from number_sequence
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('leave_category_id')->constrained('leave_categories')->onDelete('cascade');
            $table->date('date')->default(Carbon::parse('3000-06-20'));
            $table->enum('deduct_type', ['yes', 'no', 'none'])->default('none'); // from leave_categories
            $table->enum('paid', ['yes', 'no', 'none'])->default('none'); // from leave_categories
            $table->decimal('beginning_balance', 15, 2)->default(0); // init from leave history (Sum all Amount in Leave History with Deduct = Yes)
            $table->decimal('ending_balance', 15, 2)->default(0);
            $table->decimal('adjust_balance', 15, 2)->default(0); // the difference between Beginning Balance and Ending Balance
            $table->text('remark')->default('N/A');
            $table->enum('posted', ['yes', 'no'])->default('no'); // When the request is posted then Leave Request data will be added to the Leave History with TransType  'Adjustment'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_adjusments');
    }
};
