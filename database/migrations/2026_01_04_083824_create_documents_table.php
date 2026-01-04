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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();

            $table->enum('type', [
                'PAYMENT_PROOF',
                'KK',
                'BIRTH_CERT',
                'KTP_FATHER',
                'KTP_MOTHER',
                'SKTM',
                'GOOD_BEHAVIOR'
            ]);
            $table->string('file_path')->nullable();

            $table->boolean('is_required')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->text('note')->nullable();

            $table->timestamps();

            $table->unique(['registration_id', 'type']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
