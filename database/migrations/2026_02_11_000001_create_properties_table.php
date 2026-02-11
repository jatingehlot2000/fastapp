<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('property_type_id');
            $table->string('property_type_name');
            $table->string('purpose');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('area_sqft');
            $table->string('bhk_type')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->string('toilet_type')->nullable();
            $table->string('furnishing')->nullable();
            $table->string('preferred_tenant')->nullable();
            $table->integer('floor_number')->nullable();
            $table->integer('total_floors')->nullable();
            $table->string('property_age')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('locality');
            $table->string('address_line');
            $table->string('landmark')->nullable();
            $table->string('zip_code');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('monthly_rent', 10, 2);
            $table->decimal('security_deposit', 10, 2);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
