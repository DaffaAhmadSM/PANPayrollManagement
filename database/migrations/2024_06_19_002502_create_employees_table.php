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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('No');
            $table->string('Name');
            $table->enum('type', ['employee', 'freelance']);
            $table->string('search_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date');
            $table->string('birth_place');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O']);
            $table->enum('religion', ['Muslim', 'Protestant', 'Catholic', 'Hindu', 'Buddhist', 'Confucian']);
            $table->string('ethnic_group');
            $table->string('phone');
            $table->string('email');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed', 'none'])->default('none');
            $table->integer('number_of_dependents')->default(0);
            $table->enum('status', ['active', 'inactive']);
            $table->unsignedBigInteger('last_education');
            $table->foreign('last_education')->references('id')->on('education_levels');
            $table->string('clothes_size');
            $table->string('shoes_size');
            $table->decimal('entitle_leaved_per_month', 10, 2);
            $table->string('img_picture');
            $table->string('identity_number')->nullable();
            $table->string('family_card_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expired_date')->nullable();
            $table->text('tax_number')->nullable();
            $table->date('tax_start_date')->nullable();
            $table->foreignId('classification_of_tax_payer_id')->constrained('classification_of_tax_payers');
            $table->enum('tax_paid_by_company', ['yes', 'no']);
            $table->enum('tax_calculation_method', ['gross', 'net', 'gross_up','none'])->default('none');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->string('emergency_contact_address');
            $table->string('emergency_contact_relationship');
            $table->string('bank_account');
            $table->string('bank_branch');
            $table->string('bank_no');
            $table->string('bank_holder');
            $table->string('bpjs_tk')->nullable();
            $table->string('bpjs_medical')->nullable();
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
