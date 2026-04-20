<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('student_profiles', 'village')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->string('village')->nullable()->after('district');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('student_profiles', 'village')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->dropColumn('village');
            });
        }
    }
};
