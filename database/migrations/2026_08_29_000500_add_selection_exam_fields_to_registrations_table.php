<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->decimal('tahfidz_score', 5, 2)->nullable()->after('oral_exam_notes');
            $table->decimal('tajwid_score', 5, 2)->nullable()->after('tahfidz_score');
            $table->decimal('arabic_score', 5, 2)->nullable()->after('tajwid_score');
            $table->decimal('tpa_score', 5, 2)->nullable()->after('arabic_score');
            $table->enum('interview_recommendation', [
                'sangat_direkomendasikan',
                'direkomendasikan',
                'tidak_direkomendasikan',
            ])->default('direkomendasikan')->after('tpa_score');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'tahfidz_score',
                'tajwid_score',
                'arabic_score',
                'tpa_score',
                'interview_recommendation',
            ]);
        });
    }
};
