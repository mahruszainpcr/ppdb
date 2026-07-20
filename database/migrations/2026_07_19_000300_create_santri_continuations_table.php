<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri_continuations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('last_class')->default('IX Wustho');
            $table->string('dormitory');
            $table->string('father_name');
            $table->string('father_phone', 30);
            $table->string('mother_name');
            $table->string('mother_phone', 30);
            $table->boolean('continue_to_ulya')->default(true);
            $table->boolean('agree_rules')->default(false);
            $table->boolean('agree_programs')->default(false);
            $table->boolean('agree_administration')->default(false);
            $table->string('bedding_option', 20);
            $table->string('signature_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_continuations');
    }
};
