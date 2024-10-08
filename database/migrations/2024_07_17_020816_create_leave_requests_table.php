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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('number_sequence_id')->constrained('number_sequences')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('leave_category_id')->constrained('leave_categories')->onDelete('cascade');
            $table->string('no'); // from number_sequence
            $table->string('name'); // from employee
            $table->date('date_request')->default(Carbon::parse('3000-06-20'));
            $table->enum('deduct', ['yes', 'no', 'none'])->default('none'); // from leave_categories
            $table->enum('paid', ['yes', 'no', 'none'])->default('none'); // from leave_categories
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('from_date_time')->default(Carbon::parse('3000-06-20'));
            $table->date('to_date_time')->default(Carbon::parse('3000-06-20'));
            $table->string('adress_during_leave')->default('N/A');
            $table->string('contact_no')->default('N/A');
            $table->text('remark')->nullable();
            $table->enum('posted', ['yes', 'no'])->default('no'); // When the request is posted then Leave Request data will be added to the Leave History with TransType  'Leave'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
