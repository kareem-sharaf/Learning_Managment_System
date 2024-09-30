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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->integer('price')->default(0);
            $table->unsignedBigInteger('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->string('image')->nullable();
            // $table->foreignId('video_id')->nullable()->constrained('videos')->cascadeOnDelete();
            // $table->foreignId('file_id')->nullable()->constrained('files')->cascadeOnDelete();
            $table->boolean('exist')->default(true);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
