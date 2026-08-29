<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $year = date('y');

        DB::table('registrations')
            ->orderBy('id')
            ->eachById(function ($registration) use ($year) {
                DB::table('registrations')
                    ->where('id', $registration->id)
                    ->update(['registration_no' => sprintf('DS-%s-%03d', $year, $registration->id)]);
            });
    }

    public function down(): void
    {
        // Nomor lama bersifat acak dan tidak dapat direkonstruksi.
    }
};