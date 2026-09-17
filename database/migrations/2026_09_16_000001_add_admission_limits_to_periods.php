<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->unsignedInteger('scholarship_quota')->default(10);
            $table->unsignedInteger('takhosus_ikhwan_quota')->default(6);
            $table->unsignedInteger('takhosus_akhwat_quota')->default(3);
            $table->string('wa_group_takhosus_ikhwan')->nullable();
            $table->string('wa_group_takhosus_akhwat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn(['scholarship_quota', 'takhosus_ikhwan_quota', 'takhosus_akhwat_quota',
                'wa_group_takhosus_ikhwan', 'wa_group_takhosus_akhwat']);
        });
    }
};
