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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('period_id')->nullable()->constrained()->nullOnDelete();

            $table->string('registration_no')->unique();

            $table->enum('funding_type', ['mandiri', 'beasiswa'])->default('mandiri');
            $table->enum('education_level', ['SMP_NEW', 'SMA_NEW', 'SMA_OLD']);
            $table->enum('gender', ['male', 'female'])->nullable();

            $table->enum('status', ['draft', 'submitted', 'verified', 'revision_requested'])->default('draft');
            $table->enum('graduation_status', ['pending', 'lulus', 'tidak_lulus', 'cadangan'])->default('pending');

            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
