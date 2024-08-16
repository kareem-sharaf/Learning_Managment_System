<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file');
            $table->unsignedBigInteger('subject_id')->nullable()->constrained('subjects')->cascadeOnDelete();
            $table->unsignedBigInteger('unit_id')->nullable()->constrained('units')->cascadeOnDelete();
            $table->unsignedBigInteger('lesson_id')->nullable()->constrained('lessons')->cascadeOnDelete();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
