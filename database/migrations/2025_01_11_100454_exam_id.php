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
        Schema::table('student_answer', function (Blueprint $table) {
            $table->foreignId('exam_id')->nullable()->constrained('exams')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_answer', function (Blueprint $table) {
            $table->dropColumn('exam_id');
        });
    }
};
