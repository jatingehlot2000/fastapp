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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('mobile_no')->unique();
            $table->string('role')->default('user'); // owner, broker, user
            $table->string('referral_code')->unique();
            $table->string('referred_by')->nullable();
            $table->string('otp')->nullable();
            $table->boolean('verified_otp')->default(false);
            $table->boolean('login_in')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
