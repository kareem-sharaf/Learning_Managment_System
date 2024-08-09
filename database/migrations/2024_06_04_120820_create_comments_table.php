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
            $table->unsignedBigInteger('reply_to')->nullable()->constrained('comments')->cascadeOnDelete()->default(null);
            // $table->foreign('reply_to')->references('id')->on('comments')->cascadeOnDelete();
            $table->unsignedBigInteger('video_id')->nullable()->constrained('videos')->cascadeOnDelete();



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
