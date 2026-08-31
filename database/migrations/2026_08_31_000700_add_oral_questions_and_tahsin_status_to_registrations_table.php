<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->unsignedTinyInteger('oral_question_1')->default(0)->after('oral_exam_notes');
            $table->unsignedTinyInteger('oral_question_2')->default(0)->after('oral_question_1');
            $table->unsignedTinyInteger('oral_question_3')->default(0)->after('oral_question_2');
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
