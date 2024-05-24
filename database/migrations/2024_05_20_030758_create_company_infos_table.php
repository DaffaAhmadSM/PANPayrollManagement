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
        Schema::create('company_infos', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('country');
            $table->string('city');
            $table->string('post_code');
            $table->text('address');
            $table->string('phone');
            $table->string('fax');
            $table->string('email');
            $table->string('homepage');
            $table->string('img_logo');
            $table->string('bank_name');
            $table->string('bank_address');
            $table->string('bank_account_no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_infos');
    }
};
