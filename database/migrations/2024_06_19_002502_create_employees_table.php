<?php

use Carbon\Carbon;
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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('number_sequence_id')->constrained('number_sequences');
            $table->string('no')->default('N/A')->index();
            $table->string('name')->default('N/A');
            $table->enum('type', ['employee', 'freelance'])->default('employee');
            $table->string('search_name')->default('N/A');
            $table->enum('gender', ['male', 'female', 'none'])->default('none');
            $table->date('birth_date')->default(Carbon::parse('3000-12-31'));
            $table->string('birth_place')->default('N/A');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O', 'none'])->default('none');
            $table->enum('religion', ['Muslim', 'Protestant', 'Catholic', 'Hindu', 'Buddhist', 'Confucian', 'none'])->default('none');
            $table->string('ethnic_group')->default('N/A');
            $table->string('phone')->default('N/A');
            $table->string('email')->default('email@email.com');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed', 'none'])->default('none');
            $table->integer('number_of_dependents')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('last_education');
            $table->foreign('last_education')->references('id')->on('education_levels');
            $table->string('clothes_size')->default('N/A');
            $table->string('shoes_size')->default('N/A');
            $table->decimal('entitle_leaved_per_month', 10, 2)->default(0);
            $table->string('img_picture')->nullable()->default('N/A');
            $table->string('identity_number')->default('N/A');
            $table->string('family_card_number')->default('N/A');
            $table->string('passport_number')->nullable()->default('N/A');
            $table->date('passport_expired_date')->nullable()->default(Carbon::parse('3000-12-31'));
            $table->text('tax_number')->nullable()->default('N/A');
            $table->date('tax_start_date')->nullable()->default(Carbon::parse('3000-12-31'));
            $table->foreignId('classification_of_tax_payer_id')->constrained('classification_of_tax_payers');
            $table->enum('tax_paid_by_company', ['yes', 'no'])->default('no');
            $table->enum('tax_calculation_method', ['gross', 'net', 'gross_up','none'])->default('none');
            $table->string('emergency_contact_name')->default('N/A');
            $table->string('emergency_contact_phone')->default('N/A');
            $table->string('emergency_contact_address')->default('N/A');
            $table->string('emergency_contact_relationship')->default('N/A');
            $table->string('bank_account')->default('N/A');
            $table->string('bank_branch')->default('N/A');
            $table->string('bank_no')->default('N/A');
            $table->string('bank_holder')->default('N/A');
            $table->string('bpjs_tk')->default('N/A');
            $table->string('bpjs_medical')->default('N/A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
