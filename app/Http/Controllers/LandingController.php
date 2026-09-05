<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use App\Models\Period;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LandingController extends Controller
{
    public function index()
    {
        $newsPosts = NewsPost::query()
            ->with('category')
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        if ($newsPosts->isEmpty()) {
            $newsPosts = NewsPost::query()
                ->with('category')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();
        }

        return view('welcome', compact('newsPosts'));
    }

    public function ppdbInfo()
    {
        $ppdbPeriod = Period::query()->active()->latest('id')->first()
            ?? Period::query()->latest('id')->first();

        $settings = $this->getSettingsMap();
        $brochure = $this->buildPpdbBrochureData($settings);
        $ppdbAcademicYear = $this->resolveAcademicYear($ppdbPeriod);
        $dateLabel = fn($date) => $date ? $date->translatedFormat('d M Y') : 'Menunggu jadwal';
        $periodName = $ppdbPeriod?->name ?? 'Periode belum ditentukan';
        $periodWave = $ppdbPeriod?->wave;

        $registrationOpen = $ppdbPeriod?->registration_open_date;
        $registrationClose = $ppdbPeriod?->registration_close_date;
        $examDate = $ppdbPeriod?->exam_date;
        $announceDate = $ppdbPeriod?->announce_date;
        $downPaymentDeadline = $ppdbPeriod?->down_payment_deadline;

        $closeNextDayLabel = $registrationClose
            ? $registrationClose->copy()->addDay()->translatedFormat('d M Y')
            : 'Setelah penutupan periode';

        $heroMetrics = [
            [
                'value' => $dateLabel($registrationClose),
                'label' => 'Batas pendaftaran periode aktif',
            ],
            [
                'value' => $dateLabel($examDate),
                'label' => 'Jadwal ujian masuk',
            ],
            [
                'value' => $dateLabel($downPaymentDeadline),
                'label' => 'Batas daftar ulang / tanda jadi',
            ],
        ];

        $timelineItems = $this->buildTimelineItems($ppdbPeriod, $settings, $dateLabel);

        return view('public.ppdb.info', compact(
            'ppdbPeriod',
            'ppdbAcademicYear',
            'periodName',
            'periodWave',
            'closeNextDayLabel',
            'heroMetrics',
            'timelineItems',
            'brochure'
        ));
    }

    public function ranking()
    {
        $registrations = Registration::query()
            ->with(['studentProfile', 'santriContinuation'])
            ->whereNotNull('oral_question_1')
            ->where('oral_question_1', '>', 0)
            ->whereNotNull('oral_question_2')
            ->where('oral_question_2', '>', 0)
            ->whereNotNull('oral_question_3')
            ->where('oral_question_3', '>', 0)
            ->whereNotNull('tpa_score')
            ->where('tpa_score', '>', 0)
            ->whereNotNull('arabic_score')
            ->where('arabic_score', '>', 0)
            ->get()
            ->map(function (Registration $registration) {
                $average = collect([
                    $registration->oral_question_1,
                    $registration->oral_question_2,
                    $registration->oral_question_3,
                    $registration->tpa_score,
                    $registration->arabic_score,
                ])->avg();

                return [
                    'name' => $registration->studentProfile?->full_name
                        ?? $registration->santriContinuation?->full_name
                        ?? 'Nama belum tersedia',
                    'school' => $registration->studentProfile?->school_origin
                        ?? 'Ma’had Darussalam',
                    'gender' => $registration->gender,
                    'average' => round((float) $average, 2),
                ];
            })
            ->sortByDesc('average')
            ->values();

        $buildRanking = static function ($items) {
            $rank = 0;
            $previousAverage = null;

            return $items->values()->map(function (array $item, int $index) use (&$rank, &$previousAverage) {
                if ($previousAverage !== $item['average']) {
                    $rank = $index + 1;
                    $previousAverage = $item['average'];
                }

                $item['rank'] = $rank;
                return $item;
            });
        };

        $rankingIkhwan = $buildRanking($registrations->where('gender', 'male'));
        $rankingAkhwat = $buildRanking($registrations->where('gender', 'female'));

        return view('public.ranking', compact('rankingIkhwan', 'rankingAkhwat'));
    }

    private function buildTimelineItems(?Period $ppdbPeriod, array $settings, callable $dateLabel): array
    {
        $customTimeline = $this->decodeSettingValue($settings['ppdb_timeline_items'] ?? null);
        if (is_array($customTimeline) && !empty($customTimeline)) {
            $normalized = [];
            foreach ($customTimeline as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $normalized[] = [
                    'date' => (string) ($item['date'] ?? 'Menunggu jadwal'),
                    'title' => (string) ($item['title'] ?? 'Tahapan PPDB'),
                    'description' => (string) ($item['description'] ?? ''),
                ];
            }
            if (!empty($normalized)) {
                return $normalized;
            }
        }

        $registrationOpen = $ppdbPeriod?->registration_open_date;
        $registrationClose = $ppdbPeriod?->registration_close_date;
        $examDate = $ppdbPeriod?->exam_date;
        $announceDate = $ppdbPeriod?->announce_date;
        $downPaymentDeadline = $ppdbPeriod?->down_payment_deadline;

        return [
            [
                'date' => ($registrationOpen || $registrationClose)
                    ? trim(($registrationOpen ? $registrationOpen->translatedFormat('d M Y') : '-') . ' - ' . ($registrationClose ? $registrationClose->translatedFormat('d M Y') : '-'))
                    : 'Menunggu jadwal resmi',
                'title' => 'Periode Pendaftaran',
                'description' => 'Calon santri melengkapi formulir dan dokumen persyaratan sesuai ketentuan panitia.',
            ],
            [
                'date' => $dateLabel($examDate),
                'title' => 'Ujian Masuk',
                'description' => 'Seleksi kemampuan dasar, baca Al-Quran, serta tahapan wawancara sesuai kebijakan periode berjalan.',
            ],
            [
                'date' => $dateLabel($announceDate),
                'title' => 'Pengumuman Hasil',
                'description' => 'Hasil seleksi diumumkan melalui dashboard pendaftar dan kontak yang terdaftar.',
            ],
            [
                'date' => $dateLabel($downPaymentDeadline),
                'title' => 'Batas Daftar Ulang',
                'description' => 'Peserta yang lulus menyelesaikan administrasi akhir sebelum batas yang ditetapkan.',
            ],
        ];
    }

    private function resolveAcademicYear(?Period $period): string
    {
        if ($period?->name && preg_match('/(20\d{2})\s*[\/-]\s*(20\d{2})/', $period->name, $matches)) {
            return $matches[1] . '/' . $matches[2];
        }

        if ($period?->name && preg_match('/(20\d{2})/', $period->name, $matches)) {
            $startYear = (int) $matches[1];
            return $startYear . '/' . ($startYear + 1);
        }

        if ($period?->registration_open_date) {
            $startYear = (int) $period->registration_open_date->format('Y');
            return $startYear . '/' . ($startYear + 1);
        }

        $currentYear = (int) now()->format('Y');
        return $currentYear . '/' . ($currentYear + 1);
    }

    private function buildPpdbBrochureData(array $settings): array
    {
        return [
            'foundation' => $this->settingString($settings, 'ppdb_foundation', 'Yayasan Al-Marwa Riau'),
            'school_name' => $this->settingString($settings, 'ppdb_school_name', "Ma'had Darussalam Al-Islami Rumbai"),
            'npsn' => $this->settingString($settings, 'ppdb_npsn', '70034877'),
            'nspp' => $this->settingString($settings, 'ppdb_nspp', '510014710047'),
            'vision' => $this->settingString(
                $settings,
                'ppdb_vision',
                "Terwujudnya insan religius yang cerdas dan berakhlak mulia berdasarkan Al-Qur'an dan As-Sunnah sesuai pemahaman Salaful Ummah."
            ),
            'missions' => $this->settingArray($settings, 'ppdb_missions', [
                'Mendidik santri agar memiliki kemantapan aqidah, keluasan ilmu pengetahuan, keluhuran akhlak, dan keterampilan.',
                'Membantu anak-anak yatim piatu, fakir, miskin, muallaf dengan pendidikan murah sampai mandiri.',
                'Pengembangan kemampuan kewirausahaan santri dan kemandirian.',
                'Mengembangkan kemitraan dengan institusi pemerintah maupun swasta, regional maupun internasional.',
            ]),
            'featured_values' => $this->settingArray($settings, 'ppdb_featured_values', [
                "Manhaj salaf: implementasi ilmu dan amal sesuai Al-Qur'an dan As-Sunnah.",
                'Program bahasa Arab sehari-hari.',
                "Pengajar Al-Qur'an bersanad.",
                'Asatidz dari berbagai institusi nasional dan internasional.',
                'Zero Bullying Policy.',
                'Sistem pendidikan kesetaraan di bawah Kementerian Agama RI untuk Wustho.',
                "Sistem pendidikan Ma'had 6 tahun ditambah 1 tahun pengabdian.",
            ]),
            'pillar_title' => $this->settingString($settings, 'ppdb_pillar_title', '3 Pilar dan SAPA SAHABAT'),
            'pillars' => $this->settingArray($settings, 'ppdb_pillars', [
                '3 Pilar Darussalam: Al-Quran, Bahasa Arab, Adab.',
                'Karakter Darussalam.',
                'SAPA SAHABAT: Salafi, Peduli, Amanah, Saling Hormat, Adil, Bertanggung Jawab.',
            ]),
            'facilities_main' => $this->settingArray($settings, 'ppdb_facilities_main', [
                'Masjid ikhwan dan musholla akhwat ber-AC.',
                'UKS dan ruang observasi (full AC).',
                'Darussalam Sport Center (DSC).',
                'Tempat makan santri yang nyaman dan bersih.',
                'Asrama yang bersih dan nyaman.',
                'Ruang kelas yang nyaman dan bersih.',
                'Water treating plant (sumber air minum yang bersih).',
                'Dmart (Darussalam Mart), kantin yang menjual jajanan sehat.',
            ]),
            'facilities_extra' => $this->settingArray($settings, 'ppdb_facilities_extra', [
                'Pengamanan CCTV 24 jam.',
                'Full clustered.',
                'Lingkungan yang asri.',
                'Pengamanan security 24 jam.',
            ]),
            'levels' => $this->settingArray($settings, 'ppdb_levels', [
                'Wustho: SMP Kelas 7',
                'Ulya: SMA Kelas 10',
            ]),
            'programs' => $this->settingArray($settings, 'ppdb_programs', [
                "Program Ma'had",
                'Program Takhosus (maksimal 3 santri/wati)',
            ]),
            'test_materials' => $this->settingArray($settings, 'ppdb_test_materials', [
                "Hafalan Al-Qur'an minimal 1 juz (SMP) dan 6 juz (SMA).",
                'Tahsin.',
                'Khusus Ulya dan santri pindahan: placement Bahasa Arab.',
                'Tes pengetahuan umum.',
            ]),
            'registration_fee' => $this->settingString($settings, 'ppdb_registration_fee', 'Biaya pendaftaran Rp150.000, tempat terbatas.'),
            'registration_fee_account' => $this->settingString($settings, 'ppdb_registration_fee_account', 'BSI 7145-1777-28 a.n. Al-Marwa'),
            'registration_link' => $this->settingString($settings, 'ppdb_registration_link', 'https://mahaddarussalampalas.ponpes.id/login'),
            'santri_lama' => $this->settingString($settings, 'ppdb_santri_lama', 'Santri lama (menyambung ke Ulya/SMA): membayar Rp1.500.000.'),
            'beasiswa' => $this->settingArray($settings, 'ppdb_beasiswa_items', [
                'Uang masuk Rp4.500.000 (tidak termasuk SPP Juli).',
                'Uang seragam dan buku Rp1.750.000.',
                'SPP Rp650.000 (SMP) dan Rp750.000 (SMA).',
            ]),
            'mandiri' => $this->settingArray($settings, 'ppdb_mandiri_items', [
                'Uang masuk Rp9.500.000 (tidak termasuk SPP Juli).',
                'Uang seragam dan buku Rp1.750.000.',
                'SPP Rp1.200.000 (SMP) dan Rp1.300.000 (SMA).',
            ]),
            'hafalan_beasiswa' => $this->settingArray($settings, 'ppdb_hafalan_beasiswa', [
                'Minimal 3 juz untuk SMP.',
                'Minimal 12 juz untuk Ulya.',
            ]),
            'installment_note' => $this->settingString(
                $settings,
                'ppdb_installment_note',
                "Uang infak masuk ma'had bisa dicicil: pembayaran pertama minimal Rp4.000.000 (mandiri) dan Rp1.000.000 (beasiswa)."
            ),
            'notes' => $this->settingArray($settings, 'ppdb_notes', [
                'Semua santri/santriwati wajib menandatangani pernyataan kesediaan mengikuti program pengabdian.',
                'Khusus santri beasiswa full dan semi subsidi bersedia mengabdi di lingkungan ma’had dengan tanda tangan materai.',
                "Untuk santri Darussalam yang melanjutkan dari Wustho ke Ulya gratis uang masuk, cukup membayar uang sarana prasarana Rp1.500.000.",
            ]),
            'contacts_ikhwan' => $this->settingArray($settings, 'ppdb_contacts_ikhwan', [
                "Ustadz Abu Ja'far: 0821-7267-6721",
                'Ustadz Haryadi: 0812-6790-435',
                'Ustadz Margolong: 0812-7770-0212',
                'Admin: 0821-1792-7452',
            ]),
            'contacts_akhwat' => $this->settingArray($settings, 'ppdb_contacts_akhwat', [
                'Ustadzah Rina: 0877-9502-1625',
            ]),
            'address' => $this->settingString($settings, 'ppdb_address', 'Jl. Perjuangan, Kelurahan Palas, Kecamatan Rumbai'),
            'social_handle' => $this->settingString($settings, 'ppdb_social_handle', '@mahaddarussalamalislami'),
        ];
    }

    private function getSettingsMap(): array
    {
        if (!Schema::hasTable('settings')) {
            return [];
        }

        $columns = Schema::getColumnListing('settings');
        $keyColumn = in_array('key', $columns, true) ? 'key' : (in_array('name', $columns, true) ? 'name' : null);
        $valueColumn = in_array('value', $columns, true) ? 'value' : (in_array('content', $columns, true) ? 'content' : null);

        if (!$keyColumn || !$valueColumn) {
            return [];
        }

        $rows = DB::table('settings')->select([$keyColumn, $valueColumn])->get();
        $mapped = [];

        foreach ($rows as $row) {
            $key = (string) data_get($row, $keyColumn);
            if ($key === '') {
                continue;
            }

            $mapped[$key] = data_get($row, $valueColumn);
        }

        return $mapped;
    }

    private function settingString(array $settings, string $key, string $default): string
    {
        $value = $settings[$key] ?? null;
        if (!is_string($value)) {
            return $default;
        }

        $trimmed = trim($value);
        return $trimmed !== '' ? $trimmed : $default;
    }

    private function settingArray(array $settings, string $key, array $default): array
    {
        $decoded = $this->decodeSettingValue($settings[$key] ?? null);
        if (!is_array($decoded) || empty($decoded)) {
            return $default;
        }

        $normalized = [];
        foreach ($decoded as $item) {
            if (is_string($item)) {
                $item = trim($item);
                if ($item !== '') {
                    $normalized[] = $item;
                }
            }
        }

        return !empty($normalized) ? $normalized : $default;
    }

    private function decodeSettingValue(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return $value;
        }

        $first = $trimmed[0] ?? '';
        if ($first !== '[' && $first !== '{') {
            return $value;
        }

        $decoded = json_decode($trimmed, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }
}
