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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('full_name');
            $table->string('nisn')->nullable();
            $table->string('nik')->nullable();

            $table->string('birth_place');
            $table->date('birth_date');

            $table->text('address');
            $table->string('province');
            $table->string('city');
            $table->string('district');
            $table->string('postal_code')->nullable();

            $table->string('school_origin');
            $table->string('hobby');
            $table->string('ambition');

            $table->string('religion')->default('ISLAM');
            $table->string('nationality')->default('INDONESIA');

            $table->unsignedTinyInteger('siblings_count')->nullable();
            $table->unsignedTinyInteger('child_number')->nullable();

            $table->enum('orphan_status', ['both', 'yatim', 'piatu', 'yatim_piatu'])->default('both');
            $table->string('blood_type')->nullable();

            $table->string('medical_history'); // isi "Tidak" jika tidak ada
            $table->enum('motivation', ['self', 'parents']);
            $table->enum('quran_memorization_level', ['lt_half', 'lt_one', 'ge_one', 'ge_three', 'ge_five']);
            $table->enum('quran_reading_level', ['none', 'iqro', 'fluent', 'fluent_tahsin']);

            $table->enum('program_choice', ['mahad', 'takhosus']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
