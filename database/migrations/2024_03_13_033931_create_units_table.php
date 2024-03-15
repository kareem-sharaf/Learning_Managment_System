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
            $table->increments('id');
            $table->string('name');
            $table->json('content')->nullable();
            $table->binary('image_data')->default('default_image.jpg');
           // $table->byte('video');
           $table->integer('price')->nullable();
           $table->integer('subject_id')->unsigned();
           $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
          // $table->integer('lesson_id')->unsigned();
           //$table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
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
