<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Period;
use App\Models\Registration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    private const DOCUMENT_TYPES = [
        'PAYMENT_PROOF' => 'PAYMENT_PROOF',
        'KK' => 'KK',
        'BIRTH_CERT' => 'BIRTH_CERT',
        'KTP_FATHER' => 'KTP_FATHER',
        'KTP_MOTHER' => 'KTP_MOTHER',
        'SKTM' => 'SKTM',
        'GOOD_BEHAVIOR' => 'GOOD_BEHAVIOR',
    ];

    public function index(Request $request)
    {
        $periodId = $request->query('period_id');

        $periods = Period::query()
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->get();

        $selectedPeriod = $periodId
            ? $periods->firstWhere('id', (int) $periodId)
            : ($periods->firstWhere('is_active', true) ?? $periods->first());

        $baseRegistrations = Registration::query();
        if ($periodId) {
            $baseRegistrations->where('period_id', $periodId);
        }

        $totalRegistrations = (clone $baseRegistrations)->count();

        $statusCounts = (clone $baseRegistrations)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $graduationCounts = (clone $baseRegistrations)
            ->select('graduation_status', DB::raw('count(*) as total'))
            ->groupBy('graduation_status')
            ->pluck('total', 'graduation_status')
            ->toArray();

        $genderCounts = (clone $baseRegistrations)
            ->whereNotNull('gender')
            ->select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->pluck('total', 'gender')
            ->toArray();

        $educationCounts = (clone $baseRegistrations)
            ->select('education_level', DB::raw('count(*) as total'))
            ->groupBy('education_level')
            ->pluck('total', 'education_level')
            ->toArray();

        $fundingCounts = (clone $baseRegistrations)
            ->select('funding_type', DB::raw('count(*) as total'))
            ->groupBy('funding_type')
            ->pluck('total', 'funding_type')
            ->toArray();

        $studentProfileBase = DB::table('student_profiles')
            ->join('registrations', 'student_profiles.registration_id', '=', 'registrations.id')
            ->when($periodId, fn($q) => $q->where('registrations.period_id', $periodId));

        $programCounts = (clone $studentProfileBase)
            ->select('student_profiles.program_choice', DB::raw('count(*) as total'))
            ->groupBy('student_profiles.program_choice')
            ->pluck('total', 'program_choice')
            ->toArray();

        $quranReadingCounts = (clone $studentProfileBase)
            ->select('student_profiles.quran_reading_level', DB::raw('count(*) as total'))
            ->groupBy('student_profiles.quran_reading_level')
            ->pluck('total', 'quran_reading_level')
            ->toArray();

        $paymentUploaded = DB::table('documents')
            ->join('registrations', 'documents.registration_id', '=', 'registrations.id')
            ->where('documents.type', 'PAYMENT_PROOF')
            ->whereNotNull('documents.file_path')
            ->when($periodId, fn($q) => $q->where('registrations.period_id', $periodId))
            ->distinct('documents.registration_id')
            ->count('documents.registration_id');

        $paymentVerified = DB::table('documents')
            ->join('registrations', 'documents.registration_id', '=', 'registrations.id')
            ->where('documents.type', 'PAYMENT_PROOF')
            ->whereNotNull('documents.file_path')
            ->where('documents.is_verified', true)
            ->when($periodId, fn($q) => $q->where('registrations.period_id', $periodId))
            ->distinct('documents.registration_id')
            ->count('documents.registration_id');

        $paymentPending = max($totalRegistrations - $paymentUploaded, 0);

        $topSchools = (clone $studentProfileBase)
            ->select(
                'student_profiles.school_origin as school',
                'student_profiles.city as city',
                DB::raw('count(*) as total')
            )
            ->groupBy('student_profiles.school_origin', 'student_profiles.city')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $topCities = (clone $studentProfileBase)
            ->select('student_profiles.city as label', DB::raw('count(*) as total'))
            ->groupBy('student_profiles.city')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $topDistricts = (clone $studentProfileBase)
            ->select('student_profiles.district as label', DB::raw('count(*) as total'))
            ->groupBy('student_profiles.district')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $trendStart = $selectedPeriod?->registration_open_date
            ? Carbon::parse($selectedPeriod->registration_open_date)->startOfDay()
            : now()->subDays(30)->startOfDay();
        $trendEnd = $selectedPeriod?->registration_close_date
            ? Carbon::parse($selectedPeriod->registration_close_date)->endOfDay()
            : now()->endOfDay();

        if ($trendStart->diffInDays($trendEnd) > 45) {
            $trendStart = now()->subDays(30)->startOfDay();
            $trendEnd = now()->endOfDay();
        }

        $trendRows = (clone $baseRegistrations)
            ->selectRaw('DATE(created_at) as registration_date, COUNT(*) as total')
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('registration_date')
            ->get()
            ->keyBy('registration_date');

        $trendLabels = [];
        $trendSeries = [];
        for ($date = $trendStart->copy(); $date->lte($trendEnd->copy()->startOfDay()); $date->addDay()) {
            $key = $date->toDateString();
            $trendLabels[] = $date->translatedFormat('j M');
            $trendSeries[] = (int) data_get($trendRows, $key . '.total', 0);
        }

        $documentStatsRaw = DB::table('documents')
            ->join('registrations', 'documents.registration_id', '=', 'registrations.id')
            ->select(
                'documents.type',
                DB::raw('COUNT(*) as rows_total'),
                DB::raw('SUM(CASE WHEN documents.is_required = 1 THEN 1 ELSE 0 END) as required_total'),
                DB::raw('SUM(CASE WHEN documents.file_path IS NOT NULL THEN 1 ELSE 0 END) as uploaded_total'),
                DB::raw('SUM(CASE WHEN documents.file_path IS NOT NULL AND documents.is_verified = 1 THEN 1 ELSE 0 END) as verified_total')
            )
            ->whereIn('documents.type', array_keys(self::DOCUMENT_TYPES))
            ->when($periodId, fn($q) => $q->where('registrations.period_id', $periodId))
            ->groupBy('documents.type')
            ->get()
            ->keyBy('type');

        $documentMetrics = collect(self::DOCUMENT_TYPES)->map(function (string $label, string $type) use ($documentStatsRaw, $totalRegistrations) {
            $required = (int) data_get($documentStatsRaw, $type . '.required_total', 0);
            $uploaded = (int) data_get($documentStatsRaw, $type . '.uploaded_total', 0);
            $verified = (int) data_get($documentStatsRaw, $type . '.verified_total', 0);
            $baseRequired = $required > 0 ? $required : $totalRegistrations;
            $percent = $baseRequired > 0 ? round(($verified / $baseRequired) * 100, 1) : 0;

            return [
                'type' => $type,
                'label' => $label,
                'required' => $baseRequired,
                'uploaded' => $uploaded,
                'verified' => $verified,
                'percent' => $percent,
            ];
        })->values();

        $documentRequiredTotal = (int) $documentMetrics->sum('required');
        $documentVerifiedTotal = (int) $documentMetrics->sum('verified');
        $documentCompletionPercent = $documentRequiredTotal > 0
            ? round(($documentVerifiedTotal / $documentRequiredTotal) * 100)
            : 0;

        $genderLabels = [
            'male' => 'Laki-laki',
            'female' => 'Perempuan',
        ];

        $educationLabels = [
            'SMP_NEW' => 'SMP Baru',
            'SMA_NEW' => 'SMA Baru',
            'SMA_OLD' => 'SMP Pindahan/Lama',
        ];

        $statusLabels = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'verified' => 'Verified',
            'revision_requested' => 'Revision Requested',
        ];

        $graduationLabels = [
            'pending' => 'Pending',
            'lulus' => 'Lulus',
            'tidak_lulus' => 'Tidak Lulus',
            'cadangan' => 'Cadangan',
        ];

        $fundingLabels = [
            'mandiri' => 'Mandiri',
            'beasiswa' => 'Beasiswa',
        ];

        $programLabels = [
            'mahad' => 'Mahad',
            'takhosus' => 'Takhosus',
        ];

        $quranReadingLabels = [
            'none' => 'Pemula',
            'iqro' => 'Dasar',
            'fluent' => 'Menengah',
            'fluent_tahsin' => 'Lancar',
        ];

        $cityPercentageRows = $this->toPercentageSummary($topCities, $totalRegistrations);
        $mapPoints = collect([
            ['top' => '18%', 'left' => '10%'],
            ['top' => '36%', 'left' => '18%'],
            ['top' => '56%', 'left' => '26%'],
            ['top' => '65%', 'left' => '42%'],
            ['top' => '42%', 'left' => '56%'],
            ['top' => '52%', 'left' => '72%'],
            ['top' => '74%', 'left' => '84%'],
            ['top' => '28%', 'left' => '84%'],
        ])->zip($cityPercentageRows)->filter(function ($pair) {
            return !empty($pair[1]) && is_array($pair[1]);
        })->map(function ($pair) {
            [$position, $city] = $pair;

            return [
                'label' => $city['label'],
                'total' => $city['total'],
                'percent' => $city['percent'],
                'top' => $position['top'],
                'left' => $position['left'],
                'size' => max(34, min(66, 26 + ((int) $city['total'] / 4))),
            ];
        })->all();

        $waRegistrations = (clone $baseRegistrations)
            ->with(['studentProfile', 'parentProfile', 'statement', 'santriContinuation', 'documents'])
            ->latest('id')
            ->get();

        $registrationSummary = $this->buildRegistrationSummary($waRegistrations, $selectedPeriod);

        return view('admin.dashboard', [
            'periods' => $periods,
            'periodId' => $periodId,
            'selectedPeriod' => $selectedPeriod,
            'totalRegistrations' => $totalRegistrations,
            'statusCounts' => $statusCounts,
            'graduationCounts' => $graduationCounts,
            'genderCounts' => $genderCounts,
            'educationCounts' => $educationCounts,
            'fundingCounts' => $fundingCounts,
            'programCounts' => $programCounts,
            'quranReadingCounts' => $quranReadingCounts,
            'paymentUploaded' => $paymentUploaded,
            'paymentVerified' => $paymentVerified,
            'paymentPending' => $paymentPending,
            'topSchools' => $topSchools,
            'topCities' => $topCities,
            'topDistricts' => $topDistricts,
            'trendLabels' => $trendLabels,
            'trendSeries' => $trendSeries,
            'documentMetrics' => $documentMetrics,
            'documentRequiredTotal' => $documentRequiredTotal,
            'documentVerifiedTotal' => $documentVerifiedTotal,
            'documentCompletionPercent' => $documentCompletionPercent,
            'genderLabels' => $genderLabels,
            'educationLabels' => $educationLabels,
            'statusLabels' => $statusLabels,
            'graduationLabels' => $graduationLabels,
            'fundingLabels' => $fundingLabels,
            'programLabels' => $programLabels,
            'quranReadingLabels' => $quranReadingLabels,
            'cityPercentageRows' => $cityPercentageRows,
            'mapPoints' => $mapPoints,
            'registrationSummary' => $registrationSummary,
        ]);
    }

    private function toPercentageSummary(Collection $rows, int $total): array
    {
        return $rows->map(function ($row) use ($total) {
            $count = (int) ($row->total ?? 0);

            return [
                'label' => $row->label ?: 'Tidak diketahui',
                'total' => $count,
                'percent' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        })->values()->all();
    }

    private function buildRegistrationSummary(Collection $registrations, ?Period $period): array
    {
        $rows = $registrations->map(function (Registration $registration) {
            $studentName = $registration->studentProfile?->full_name
                ?? $registration->santriContinuation?->full_name
                ?? '-';
            $schoolOrigin = $registration->studentProfile?->school_origin ?? '-';
            $isComplete = $this->registrationIsComplete($registration);

            return [
                'registration_no' => $registration->registration_no,
                'student_name' => $studentName,
                'school_origin' => $schoolOrigin,
                'is_complete' => $isComplete,
                'status_label' => $isComplete ? 'Lengkap 100%' : 'Belum Lengkap',
            ];
        })->values();

        $completeRows = $rows->where('is_complete', true)->values();
        $incompleteRows = $rows->where('is_complete', false)->values();

        $periodLabel = $period
            ? $period->name . ' - Gelombang ' . $period->wave
            : 'Semua Pendaftar';

        $messageLines = [
            '*Ringkasan Pendaftaran Santri*',
            'Periode: ' . $periodLabel,
            'Tanggal: ' . now()->translatedFormat('d M Y H:i'),
            '',
            'Total pendaftar: *' . $rows->count() . '* santri',
            'Lengkap 100%: *' . $completeRows->count() . '* santri',
            'Belum lengkap: *' . $incompleteRows->count() . '* santri',
            '',
            '*Daftar Santri Lengkap 100%*',
        ];

        if ($completeRows->isEmpty()) {
            $messageLines[] = '- Belum ada santri yang lengkap 100%';
        } else {
            foreach ($completeRows as $index => $row) {
                $messageLines[] = ($index + 1) . '. ' . $row['student_name'] . ' - ' . $row['school_origin'];
            }
        }

        $messageLines[] = '';
        $messageLines[] = '*Daftar Santri Belum Lengkap*';

        if ($incompleteRows->isEmpty()) {
            $messageLines[] = '- Semua santri sudah lengkap';
        } else {
            foreach ($incompleteRows as $index => $row) {
                $messageLines[] = ($index + 1) . '. ' . $row['student_name'] . ' - ' . $row['school_origin'];
            }
        }

        return [
            'period_label' => $periodLabel,
            'total' => $rows->count(),
            'complete_total' => $completeRows->count(),
            'incomplete_total' => $incompleteRows->count(),
            'complete_rows' => $completeRows,
            'incomplete_rows' => $incompleteRows,
            'message' => implode("\n", $messageLines),
            'wa_url' => 'https://wa.me/?text=' . rawurlencode(implode("\n", $messageLines)),
        ];
    }

    private function registrationIsComplete(Registration $registration): bool
    {
        if ($registration->education_level === 'SMA_OLD') {
            return (bool) $registration->santriContinuation
                && (bool) $registration->santriContinuation?->payment_proof_path
                && (bool) $registration->santriContinuation?->signature_path;
        }

        return (bool) $registration->studentProfile
            && (bool) $registration->parentProfile
            && (bool) $registration->statement
            && $registration->isStep1Complete();
    }
}
