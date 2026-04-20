<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('statements', 'agree_integrity')) {
            Schema::table('statements', function (Blueprint $table) {
                $table->boolean('agree_integrity')->default(false)->after('agree_rules');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('statements', 'agree_integrity')) {
            Schema::table('statements', function (Blueprint $table) {
                $table->dropColumn('agree_integrity');
            });
        }
    }
};
