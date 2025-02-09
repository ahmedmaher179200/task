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
        Schema::create('user_exam_time_line', function (Blueprint $table) {
            $table->bigIncrements('id')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnDelete();
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_exam_time_line');
    }
};
