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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('image_data')->default('default_image.jpg')->nullable();
            $table->unsignedBigInteger('video_id')->nullable()->references('id')->on('videos')->onDelete('cascade');
            $table->unsignedBigInteger('file_id')->nullable()->references('id')->on('files')->onDelete('cascade');
            $table->unsignedBigInteger('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
