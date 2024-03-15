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
            $table->string('name');
            $table->string('father_name');
            $table->string('email')->unique()->nullable();
            $table->integer('phone_number')->unique();
            $table->string('password');
            $table->string('device_id')->unique()->nullable();
            $table->binary('image_data')->default('default_image.jpg');
            $table->string('address');
            $table->date('birth_date');
            $table->unsignedBigInteger('role_id')->constrained('roles')->cascadeOnDelete();
            $table->unsignedBigInteger('stage_id')->constrained('stages')->cascadeOnDelete()->nullable();
            $table->unsignedBigInteger('year_id')->constrained('years')->cascadeOnDelete()->nullable();
            $table->integer('points')->default(0);
            $table->rememberToken();
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
