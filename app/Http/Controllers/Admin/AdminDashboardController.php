<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Period;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $periodId = $request->query('period_id');

        $periods = Period::query()
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->get();

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

        $topSchools = DB::table('student_profiles')
            ->join('registrations', 'student_profiles.registration_id', '=', 'registrations.id')
            ->select('student_profiles.school_origin as label', DB::raw('count(*) as total'))
            ->when($periodId, fn($q) => $q->where('registrations.period_id', $periodId))
            ->groupBy('student_profiles.school_origin')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topCities = DB::table('student_profiles')
            ->join('registrations', 'student_profiles.registration_id', '=', 'registrations.id')
            ->select('student_profiles.city as label', DB::raw('count(*) as total'))
            ->when($periodId, fn($q) => $q->where('registrations.period_id', $periodId))
            ->groupBy('student_profiles.city')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topDistricts = DB::table('student_profiles')
            ->join('registrations', 'student_profiles.registration_id', '=', 'registrations.id')
            ->select('student_profiles.district as label', DB::raw('count(*) as total'))
            ->when($periodId, fn($q) => $q->where('registrations.period_id', $periodId))
            ->groupBy('student_profiles.district')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $educationGrouped = [
            'SMP' => ($educationCounts['SMP_NEW'] ?? 0),
            'SMA' => ($educationCounts['SMA_NEW'] ?? 0) + ($educationCounts['SMA_OLD'] ?? 0),
        ];

        $genderLabels = [
            'male' => 'Ikhwan',
            'female' => 'Akhwat',
        ];

        $statusLabels = [
            'draft' => 'Draft',
            'submitted' => 'Terkirim',
            'verified' => 'Terverifikasi',
            'revision_requested' => 'Revisi',
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

        return view('admin.dashboard', [
            'periods' => $periods,
            'periodId' => $periodId,
            'totalRegistrations' => $totalRegistrations,
            'statusCounts' => $statusCounts,
            'graduationCounts' => $graduationCounts,
            'genderCounts' => $genderCounts,
            'educationCounts' => $educationCounts,
            'educationGrouped' => $educationGrouped,
            'fundingCounts' => $fundingCounts,
            'paymentUploaded' => $paymentUploaded,
            'paymentVerified' => $paymentVerified,
            'paymentPending' => $paymentPending,
            'topSchools' => $topSchools,
            'topCities' => $topCities,
            'topDistricts' => $topDistricts,
            'genderLabels' => $genderLabels,
            'statusLabels' => $statusLabels,
            'graduationLabels' => $graduationLabels,
            'fundingLabels' => $fundingLabels,
        ]);
    }
}
