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
        Schema::create('subject_year', function (Blueprint $table) {
            $table->id();
            /*$table->integer('year_id');
            $table->integer('subject_id');*/
            $table->unsignedBigInteger('year_id')->constrained('years')->cascadeOnDelete();
            $table->unsignedBigInteger('subject_id')->constrained('subject')->cascadeOnDelete();
            $table->unique(['subject_id', 'year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_year');
    }
};
