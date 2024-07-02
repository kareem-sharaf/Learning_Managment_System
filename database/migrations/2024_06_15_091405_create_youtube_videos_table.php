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
        Schema::create('youtube_videos', function (Blueprint $table) {
            $table->id();
            $table->string('video_id');
            $table->string('title');
            $table->string('description');
            $table->string('thumbnail_url');
            $table->string('video_url');
            $table->integer('views');
            $table->integer('likes');
            $table->integer('dislikes');
            $table->string('category_id');
            $table->string('privacy_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('youtube_videos');
    }
};
