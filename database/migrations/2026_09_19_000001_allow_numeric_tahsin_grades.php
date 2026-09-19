<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Retain legacy letter grades until an examiner replaces them with a score.
        Schema::table('ppdb_oral_exams', function (Blueprint $table) {
            foreach ([1, 2, 3] as $number) {
                $table->string("question_{$number}_grade", 6)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        foreach ([1, 2, 3] as $number) {
            if (DB::table('ppdb_oral_exams')->whereNotNull("question_{$number}_grade")
                ->whereNotIn("question_{$number}_grade", ['A', 'B', 'C'])->exists()) {
                throw new RuntimeException('Cannot restore letter grades while numeric tahsin scores exist.');
            }
        }

        Schema::table('ppdb_oral_exams', function (Blueprint $table) {
            foreach ([1, 2, 3] as $number) {
                $table->enum("question_{$number}_grade", ['A', 'B', 'C'])->nullable()->change();
            }
        });
    }
};
