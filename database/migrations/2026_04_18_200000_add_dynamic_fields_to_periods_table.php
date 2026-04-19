<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->date('registration_open_date')->nullable()->after('is_active');
            $table->date('registration_close_date')->nullable()->after('registration_open_date');
            $table->text('information_note')->nullable()->after('admin_contact_2');
        });
    }

    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn([
                'registration_open_date',
                'registration_close_date',
                'information_note',
            ]);
        });
    }
};

