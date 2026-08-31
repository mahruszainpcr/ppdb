<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->text('oral_question_1')->nullable()->after('oral_exam_notes');
            $table->text('oral_question_2')->nullable()->after('oral_question_1');
            $table->text('oral_question_3')->nullable()->after('oral_question_2');
            $table->enum('tahsin_status', ['diterima', 'tidak_diterima', 'pending'])
                ->default('pending')
                ->after('oral_question_3');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'oral_question_1',
                'oral_question_2',
                'oral_question_3',
                'tahsin_status',
            ]);
        });
    }
};
