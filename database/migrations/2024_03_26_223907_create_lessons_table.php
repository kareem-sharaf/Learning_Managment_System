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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('teacher_id')->references('id')->on('users')->cascadeOnDelete();
            $table->integer('price');
            $table->text('description');
            $table->string('image')->nullable();
            $table->foreignId('video_id')->nullable()->constrained('videos')->cascadeOnDelete();
            $table->foreignId('file_id')->nullable()->constrained('files')->cascadeOnDelete();
            $table->boolean('exist')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
