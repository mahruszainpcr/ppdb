<?php

// app/Http/Controllers/Admin/RegistrationAdminController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegistrationAdminController extends Controller
{
    private array $documentTypes = [
        'PAYMENT_PROOF' => 'Bukti Pembayaran',
        'KK' => 'Kartu Keluarga',
        'BIRTH_CERT' => 'Akte Lahir',
        'KTP_FATHER' => 'KTP Ayah',
        'KTP_MOTHER' => 'KTP Ibu',
        'SKTM' => 'SKTM',
        'GOOD_BEHAVIOR' => 'Surat Kelakuan Baik',
    ];

    public function index(Request $request)
    {
        return view('admin.registrations.index');
    }

    public function data(Request $request)
    {
        $baseQuery = Registration::query()->with(['user', 'studentProfile']);
        $recordsTotal = (clone $baseQuery)->count();

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($q) use ($s) {
                $q->where('registration_no', 'like', "%{$s}%")
                    ->orWhereHas('studentProfile', fn($qq) => $qq->where('full_name', 'like', "%{$s}%"))
                    ->orWhereHas('user', fn($qq) => $qq->where('phone', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('period_id')) {
            $baseQuery->where('period_id', $request->period_id);
        }
        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }
        if ($request->filled('graduation_status')) {
            $baseQuery->where('graduation_status', $request->graduation_status);
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $columns = [
            0 => 'registration_no',
            3 => 'education_level',
            4 => 'status',
            5 => 'graduation_status',
        ];
        $orderColumn = $columns[$request->input('order.0.column')] ?? 'created_at';
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 15);
        if ($length <= 0) {
            $length = 15;
        }

        $registrations = $baseQuery
            ->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $registrations->map(function (Registration $r) {
            $studentName = e(optional($r->studentProfile)->full_name ?? '-');
            $phone = e($r->user->phone ?? '-');
            $status = e($r->status ?? '-');
            $graduation = e($r->graduation_status ?? '-');
            $detailUrl = route('admin.registrations.show', $r);

            return [
                'registration_no' => e($r->registration_no ?? '-'),
                'student_name' => $studentName,
                'phone' => $phone,
                'education_level' => e($r->education_level ?? '-'),
                'status' => '<span class="badge bg-secondary">' . $status . '</span>',
                'graduation_status' => '<span class="badge bg-info">' . $graduation . '</span>',
                'actions' => '<a class="btn btn-sm btn-outline-light" href="' . $detailUrl . '">Detail</a>',
            ];
        })->values();

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function export(Request $request)
    {
        $baseQuery = Registration::query()
            ->with(['user', 'studentProfile', 'parentProfile', 'period', 'statement', 'documents']);

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($q) use ($s) {
                $q->where('registration_no', 'like', "%{$s}%")
                    ->orWhereHas('studentProfile', fn($qq) => $qq->where('full_name', 'like', "%{$s}%"))
                    ->orWhereHas('user', fn($qq) => $qq->where('phone', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('period_id')) {
            $baseQuery->where('period_id', $request->period_id);
        }
        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }
        if ($request->filled('graduation_status')) {
            $baseQuery->where('graduation_status', $request->graduation_status);
        }

        $fileName = 'pendaftaran-' . now()->format('Ymd-His') . '.csv';

        $documentTypes = $this->documentTypes;

        return response()->streamDownload(function () use ($baseQuery, $documentTypes) {
            $handle = fopen('php://output', 'w');

            $header = [
                'No Pendaftaran',
                'Nama Santri',
                'NISN',
                'NIK',
                'Tempat Lahir',
                'Tanggal Lahir',
                'Jenis Kelamin',
                'Jenjang',
                'Asal Sekolah',
                'Hobi',
                'Cita-cita',
                'Agama',
                'Kewarganegaraan',
                'Jumlah Saudara',
                'Anak Ke',
                'Status Yatim',
                'Golongan Darah',
                'Riwayat Penyakit',
                'Motivasi',
                'Level Hafalan Al-Quran',
                'Level Baca Al-Quran',
                'Pilihan Program',
                'Provinsi',
                'Kota',
                'Kecamatan',
                'Alamat',
                'Kode Pos',
                'No WA Wali',
                'No KK',
                'Nama Ayah',
                'NIK Ayah',
                'Tempat Lahir Ayah',
                'Tanggal Lahir Ayah',
                'Agama Ayah',
                'Pendidikan Ayah',
                'Pekerjaan Ayah',
                'Penghasilan Ayah',
                'Alamat Ayah',
                'Kota Ayah',
                'Kecamatan Ayah',
                'Kode Pos Ayah',
                'No HP Ayah',
                'Nama Ibu',
                'NIK Ibu',
                'Tempat Lahir Ibu',
                'Tanggal Lahir Ibu',
                'Agama Ibu',
                'Pendidikan Ibu',
                'Pekerjaan Ibu',
                'Penghasilan Ibu',
                'No HP Ibu',
                'Ustadz Favorit',
                'Jenis Pembiayaan',
                'Status Pendaftaran',
                'Status Kelulusan',
                'Periode',
                'Gelombang',
                'Tanggal Daftar',
                'Pernyataan Bersedia Mengabdi',
                'Pernyataan Akhlak',
                'Pernyataan Tata Tertib',
                'Pernyataan Pembayaran',
                'Pernyataan Submit',
                'Pembayaran Upload',
                'Pembayaran Terverifikasi',
            ];

            foreach (array_values($documentTypes) as $label) {
                $header[] = 'Dokumen: ' . $label;
            }

            fputcsv($handle, $header);

            $baseQuery->orderBy('id')->chunk(200, function ($registrations) use ($handle, $documentTypes) {
                foreach ($registrations as $r) {
                    $student = $r->studentProfile;
                    $parent = $r->parentProfile;
                    $statement = $r->statement;
                    $docsByType = $r->documents->keyBy('type');

                    $paymentDoc = $docsByType->get('PAYMENT_PROOF');
                    $paymentUploaded = $paymentDoc && $paymentDoc->file_path ? 'Ya' : 'Tidak';
                    $paymentVerified = $paymentDoc && $paymentDoc->file_path && $paymentDoc->is_verified ? 'Ya' : 'Tidak';
                    $boolLabel = fn($value) => $value ? 'Ya' : 'Tidak';

                    $row = [
                        $r->registration_no ?? '',
                        optional($student)->full_name ?? '',
                        optional($student)->nisn ?? '',
                        optional($student)->nik ?? '',
                        optional($student)->birth_place ?? '',
                        optional(optional($student)->birth_date)->format('Y-m-d'),
                        $r->gender ?? '',
                        $r->education_level ?? '',
                        optional($student)->school_origin ?? '',
                        optional($student)->hobby ?? '',
                        optional($student)->ambition ?? '',
                        optional($student)->religion ?? '',
                        optional($student)->nationality ?? '',
                        optional($student)->siblings_count ?? '',
                        optional($student)->child_number ?? '',
                        optional($student)->orphan_status ?? '',
                        optional($student)->blood_type ?? '',
                        optional($student)->medical_history ?? '',
                        optional($student)->motivation ?? '',
                        optional($student)->quran_memorization_level ?? '',
                        optional($student)->quran_reading_level ?? '',
                        optional($student)->program_choice ?? '',
                        optional($student)->province ?? '',
                        optional($student)->city ?? '',
                        optional($student)->district ?? '',
                        optional($student)->address ?? '',
                        optional($student)->postal_code ?? '',
                        $r->user->phone ?? '',
                        optional($parent)->kk_number ?? '',
                        optional($parent)->father_name ?? '',
                        optional($parent)->father_nik ?? '',
                        optional($parent)->father_birth_place ?? '',
                        optional(optional($parent)->father_birth_date)->format('Y-m-d'),
                        optional($parent)->father_religion ?? '',
                        optional($parent)->father_education ?? '',
                        optional($parent)->father_job ?? '',
                        optional($parent)->father_income ?? '',
                        optional($parent)->father_address ?? '',
                        optional($parent)->father_city ?? '',
                        optional($parent)->father_district ?? '',
                        optional($parent)->father_postal_code ?? '',
                        optional($parent)->father_phone ?? '',
                        optional($parent)->mother_name ?? '',
                        optional($parent)->mother_nik ?? '',
                        optional($parent)->mother_birth_place ?? '',
                        optional(optional($parent)->mother_birth_date)->format('Y-m-d'),
                        optional($parent)->mother_religion ?? '',
                        optional($parent)->mother_education ?? '',
                        optional($parent)->mother_job ?? '',
                        optional($parent)->mother_income ?? '',
                        optional($parent)->mother_phone ?? '',
                        optional($parent)->favorite_ustadz ?? '',
                        $r->funding_type ?? '',
                        $r->status ?? '',
                        $r->graduation_status ?? '',
                        optional($r->period)->name ?? '',
                        optional($r->period)->wave ?? '',
                        optional($r->created_at)->format('Y-m-d H:i:s'),
                        $boolLabel(optional($statement)->willing_to_serve),
                        $boolLabel(optional($statement)->agree_morality),
                        $boolLabel(optional($statement)->agree_rules),
                        $boolLabel(optional($statement)->agree_payment),
                        optional(optional($statement)->submitted_at)->format('Y-m-d H:i:s'),
                        $paymentUploaded,
                        $paymentVerified,
                    ];

                    foreach (array_keys($documentTypes) as $type) {
                        $doc = $docsByType->get($type);
                        $row[] = $doc && $doc->file_path ? $doc->public_url : '';
                    }

                    fputcsv($handle, $row);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(Registration $registration)
    {
        $registration->load([
            'user',
            'period',
            'studentProfile',
            'parentProfile',
            'statement',
            'documents' => fn($q) => $q->orderBy('type'),
        ]);

        return view('admin.registrations.show', compact('registration'));
    }
    public function setGraduation(Request $request, Registration $registration)
    {
        $data = $request->validate([
            'graduation_status' => ['required', Rule::in(['pending', 'lulus', 'tidak_lulus', 'cadangan'])],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $registration->update([
            'graduation_status' => $data['graduation_status'],
            'admin_note' => $data['admin_note'] ?? null,
        ]);

        // (opsional) jika mau otomatis update status dokumen/verifikasi
        // if ($data['graduation_status'] !== 'pending') {
        //     $registration->update(['status' => 'verified']);
        // }

        return back()->with('success', 'Kelulusan berhasil diperbarui.');
    }
}
