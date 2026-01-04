<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');                // PPDB 2026 Gel 1
            $table->unsignedTinyInteger('wave');   // 1,2,3
            $table->boolean('is_active')->default(false);

            $table->date('exam_date')->nullable();
            $table->date('announce_date')->nullable();
            $table->date('down_payment_deadline')->nullable();

            $table->string('wa_group_ikhwan')->nullable();
            $table->string('wa_group_akhwat')->nullable();

            $table->string('admin_contact_1')->nullable();
            $table->string('admin_contact_2')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};
