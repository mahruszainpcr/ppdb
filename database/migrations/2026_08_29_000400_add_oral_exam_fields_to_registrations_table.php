<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->decimal('oral_quran_score', 5, 2)->nullable()->after('graduation_status');
            $table->decimal('oral_reading_score', 5, 2)->nullable()->after('oral_quran_score');
            $table->enum('oral_exam_status', ['pending', 'lulus', 'tidak_lulus'])
                ->default('pending')
                ->after('oral_reading_score');
            $table->text('oral_exam_notes')->nullable()->after('oral_exam_status');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'oral_quran_score',
                'oral_reading_score',
                'oral_exam_status',
                'oral_exam_notes',
            ]);
        });
    }
};