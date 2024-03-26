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
        Schema::create('user_validations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('father_name');
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('validation_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_validations');
    }
};
