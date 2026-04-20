<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('wilayah')) {
            Schema::create('wilayah', function (Blueprint $table) {
                $table->text('kode');
                $table->text('nama');

                $table->primary('kode');
                $table->index('nama', 'wilayah_name_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah');
    }
};
