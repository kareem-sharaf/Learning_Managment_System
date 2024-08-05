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
            $table->string('email')->unique();
            $table->timestamp('email_sent_at');
            $table->string('device_id')->unique()->nullable();
            $table->string('verificationCode')->nullable();
            $table->integer('image_id')->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('gender')->nullable();
            $table->string('password')->nullable();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('stage_id')->nullable()->constrained('stages')->cascadeOnDelete();
            $table->foreignId('year_id')->nullable()->constrained('years')->cascadeOnDelete();
            $table->integer('points')->default(0);
            $table->string('fcm')->nullable();
            $table->rememberToken();
            // $table->timestamps();
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
