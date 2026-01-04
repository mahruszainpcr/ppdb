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
        Schema::create('parent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('kk_number');

            $table->string('father_name');
            $table->string('father_nik');
            $table->string('father_birth_place');
            $table->date('father_birth_date');
            $table->string('father_religion')->default('ISLAM');
            $table->string('father_education');
            $table->string('father_job');
            $table->string('father_income');
            $table->text('father_address');
            $table->string('father_city');
            $table->string('father_district');
            $table->string('father_postal_code')->nullable();
            $table->string('father_phone');

            $table->string('mother_name');
            $table->string('mother_nik');
            $table->string('mother_birth_place');
            $table->date('mother_birth_date');
            $table->string('mother_religion')->default('ISLAM');
            $table->string('mother_education');
            $table->string('mother_job');
            $table->string('mother_income');
            $table->string('mother_phone');

            $table->string('favorite_ustadz');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_profiles');
    }
};
