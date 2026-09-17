<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_oral_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('question_1_grade', ['A', 'B', 'C'])->nullable();
            $table->enum('question_2_grade', ['A', 'B', 'C'])->nullable();
            $table->enum('question_3_grade', ['A', 'B', 'C'])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('examiner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_oral_exams');
    }
};
