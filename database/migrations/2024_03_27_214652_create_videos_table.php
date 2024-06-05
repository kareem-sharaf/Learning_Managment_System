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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('video');
            $table->unsignedBigInteger('subject_id')->nullable()->constrained('subjects')->cascadeOnDelete();
            $table->unsignedBigInteger('unit_id')->nullable()->constrained('units')->cascadeOnDelete();
            $table->unsignedBigInteger('lesson_id')->nullable()->constrained('lessons')->cascadeOnDelete();
            $table->unsignedBigInteger('ads_id')->nullable()->constrained('a_d_s')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
