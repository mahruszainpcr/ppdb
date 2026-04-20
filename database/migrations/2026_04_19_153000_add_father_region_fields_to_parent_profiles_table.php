<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parent_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('parent_profiles', 'father_province')) {
                $table->string('father_province')->nullable()->after('father_address');
            }
            if (!Schema::hasColumn('parent_profiles', 'father_village')) {
                $table->string('father_village')->nullable()->after('father_district');
            }
        });
    }

    public function down(): void
    {
        Schema::table('parent_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('parent_profiles', 'father_village')) {
                $table->dropColumn('father_village');
            }
            if (Schema::hasColumn('parent_profiles', 'father_province')) {
                $table->dropColumn('father_province');
            }
        });
    }
};
