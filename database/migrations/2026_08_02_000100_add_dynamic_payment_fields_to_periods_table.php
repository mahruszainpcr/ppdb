<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            if (!Schema::hasColumn('periods', 'payment_proof_label')) {
                $table->string('payment_proof_label')->nullable()->after('information_note');
            }

            if (!Schema::hasColumn('periods', 'payment_proof_note')) {
                $table->text('payment_proof_note')->nullable()->after('payment_proof_label');
            }

            if (!Schema::hasColumn('periods', 'payment_agreement_note')) {
                $table->text('payment_agreement_note')->nullable()->after('payment_proof_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('periods', 'payment_agreement_note') ? 'payment_agreement_note' : null,
                Schema::hasColumn('periods', 'payment_proof_note') ? 'payment_proof_note' : null,
                Schema::hasColumn('periods', 'payment_proof_label') ? 'payment_proof_label' : null,
            ]);

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
