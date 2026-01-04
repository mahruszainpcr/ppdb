<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Period;
use Carbon\Carbon;

class PeriodSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan periode lain (jika ada)
        Period::query()->update(['is_active' => false]);

        Period::create([
            'name' => 'PPDB Mahad Darussalam Al Islami 2026 - Gelombang 1',
            'wave' => 1,
            'is_active' => true,

            // Jadwal penting
            'exam_date' => Carbon::create(2025, 10, 4),        // Sabtu, 04 Oktober 2025
            'announce_date' => Carbon::create(2025, 10, 6),    // Pengumuman 06 Oktober 2025
            'down_payment_deadline' => Carbon::create(2025, 10, 11), // Tanda jadi 11 Oktober 2025

            // Grup WhatsApp
            'wa_group_ikhwan' => 'https://chat.whatsapp.com/IeA1s0tFOVY6idykeswg8i',
            'wa_group_akhwat' => 'https://chat.whatsapp.com/Kw40pZcLrEgKd0JHPPFS66',

            // Kontak admin
            'admin_contact_1' => 'Abu Ja\'far : 0821-7267-6721',
            'admin_contact_2' => 'Admin : 0821-1792-7452',

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
