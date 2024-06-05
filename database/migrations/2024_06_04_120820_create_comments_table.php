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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->unsignedBigInteger('user_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedBigInteger('lesson_id')->nullable()->constrained('lessons')->cascadeOnDelete();
            $table->unsignedBigInteger('video_id')->nullable()->constrained('videos')->cascadeOnDelete();


            $table->unsignedBigInteger('subject_id')->nullable()->constrained('subjects')->cascadeOnDelete();
            $table->unsignedBigInteger('unit_id')->nullable()->constrained('units')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
