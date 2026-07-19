<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scanned_at');
            $table->text('scan_payload')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'registration_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
    }
};
