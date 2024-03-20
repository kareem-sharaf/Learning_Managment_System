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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();            $table->string('name');
            $table->binary('image_data')->default('default_image.jpg');
            $table->string('description')->nullable();
          // $table->integer('year_id')->unsigned();
          // $table->foreign('year_id')->references('id')->on('years')->onDelete('cascade');
         //  $table->integer('stage_id')->unsigned();
        //   $table->foreign('stage_id')->references('id')->on('years')->onDelete('cascade');
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
