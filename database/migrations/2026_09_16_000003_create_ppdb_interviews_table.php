<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('answers');
            $table->boolean('distant_city_bonus')->default(false);
            $table->string('relative_details')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('base_score', 6, 2)->nullable();
            $table->unsignedTinyInteger('bonus_points')->default(0);
            $table->decimal('total_score', 6, 2)->nullable();
            $table->string('recommendation')->default('pending');
            $table->json('disqualification_reasons')->nullable();
            $table->foreignId('examiner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_interviews');
    }
};
