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
        Schema::create('teacher_subject', function (Blueprint $table) {
            $table->id();
           // $table->integer('teacher_id');
          //  $table->integer('subject_id');
            $table->unsignedBigInteger('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->unsignedBigInteger('subject_id')->constrained('subjects')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_subject');
    }
};
