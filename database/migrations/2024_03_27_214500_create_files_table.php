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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD:database/migrations/2024_03_27_214500_create_files_table.php
            $table->string('name');
            $table->string('file');
           
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
=======
            $table->string('title');
            $table->integer('price')->default(0);
            $table->binary('image_data')->default('default_image.jpg')->nullable();
            $table->unsignedBigInteger('video_id')->nullable()->references('id')->on('videos')->onDelete('cascade');
            $table->unsignedBigInteger('file_id')->nullable()->references('id')->on('files')->onDelete('cascade');
>>>>>>> fefbaad9742d2944ddba344703cebbf7303bd058:database/migrations/2024_03_20_025228_create_leasons_table.php

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
