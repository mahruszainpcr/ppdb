<?php

// app/Http/Controllers/Admin/RegistrationAdminController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\ParentProfile;
use App\Models\Registration;
use App\Models\Statement;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
        $baseQuery = Registration::query()->with(['user', 'studentProfile', 'parentProfile', 'statement', 'documents']);
        $recordsTotal = (clone $baseQuery)->count();
        $globalStatsRegistrations = Registration::query()
            ->with(['studentProfile', 'parentProfile', 'statement', 'documents'])
            ->orderBy('id')
            ->get();
        $globalStats = [
            'total' => $globalStatsRegistrations->count(),
            'lengkap' => 0,
            'kurang' => 0,
            'belum_isi' => 0,
        ];
        foreach ($globalStatsRegistrations as $item) {
            $status = $this->registrationCompletionStatus($this->registrationProgressPercent($item));
            if ($status === 'Lengkap') {
                $globalStats['lengkap']++;
            } elseif ($status === 'Kurang') {
                $globalStats['kurang']++;
            } else {
                $globalStats['belum_isi']++;
            }
        }

        $search = $request->input('search');
        if (is_array($search)) {
            $search = $search['value'] ?? null;
        }
        if ($search) {
            $s = trim($search);
            $baseQuery->where(function ($q) use ($s) {
                $q->where('registration_no', 'like', "%{$s}%")
                    ->orWhereHas('studentProfile', fn($qq) => $qq->where('full_name', 'like', "%{$s}%"))
                    ->orWhereHas('user', fn($qq) => $qq->where('name', 'like', "%{$s}%"))
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
            1 => 'registration_no',
            3 => 'gender',
            5 => 'created_at',
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

        $canDelete = $request->user()?->role === 'admin';

        $data = $registrations->values()->map(function (Registration $r, int $index) use ($start, $canDelete) {
            $studentName = e(optional($r->studentProfile)->full_name ?? '-');
            $detailUrl = route('admin.registrations.show', $r);
            $editUrl = route('admin.registrations.edit', $r);
            $deleteUrl = route('admin.registrations.destroy', $r);
            $progress = $this->registrationProgressPercent($r);
            $completionStatus = $this->registrationCompletionStatus($progress);
            $completionBadgeClass = match ($completionStatus) {
                'Lengkap' => 'bg-success',
                'Kurang' => 'bg-warning text-dark',
                default => 'bg-secondary',
            };

            $actions = '<div class="d-flex justify-content-end gap-2">'
                . '<a class="btn btn-sm btn-outline-light" href="' . $detailUrl . '">Detail</a>'
                . '<a class="btn btn-sm btn-outline-primary" href="' . $editUrl . '">Edit</a>';

            if ($canDelete) {
                $actions .= '<form method="POST" action="' . $deleteUrl . '" onsubmit="return confirm(\'Yakin hapus data pendaftaran ini?\')">'
                    . '<input type="hidden" name="_token" value="' . csrf_token() . '">'
                    . '<input type="hidden" name="_method" value="DELETE">'
                    . '<button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>'
                    . '</form>';
            }

            $actions .= '</div>';

            return [
                'row_no' => $start + $index + 1,
                'registration_no' => e($r->registration_no ?? '-'),
                'student_name' => $studentName,
                'parent_name' => e(optional($r->user)->name ?? '-'),
                'gender_group' => e($this->genderGroupLabel($r->gender)),
                'school_origin' => e(optional($r->studentProfile)->school_origin ?? '-'),
                'completion_status' => '<div><span class="badge ' . $completionBadgeClass . '">' . e($completionStatus) . '</span><div class="small text-muted mt-1">' . $progress . '%</div></div>',
                'registered_at' => e(optional($r->created_at)->format('d-m-Y H:i') ?? '-'),
                'actions' => $actions,
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
            'stats' => $globalStats,
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
                'Kategori',
                'Asal SD',
                'Status Kelengkapan',
                'Progress (%)',
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
                'Provinsi Ayah',
                'Kota Ayah',
                'Kecamatan Ayah',
                'Desa Ayah',
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
                        $this->genderGroupLabel($r->gender),
                        optional($student)->school_origin ?? '',
                        $this->registrationCompletionStatus($this->registrationProgressPercent($r)),
                        $this->registrationProgressPercent($r),
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
                        optional($parent)->father_province ?? '',
                        optional($parent)->father_city ?? '',
                        optional($parent)->father_district ?? '',
                        optional($parent)->father_village ?? '',
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

    private function registrationProgressPercent(Registration $registration): int
    {
        $step1Complete = $registration->isStep1Complete();
        $step2Complete = (bool) $registration->studentProfile;
        $step3Complete = (bool) $registration->parentProfile && (bool) $registration->statement;
        $stepsDone = collect([$step1Complete, $step2Complete, $step3Complete])->filter()->count();

        return (int) round(($stepsDone / 3) * 100);
    }

    private function registrationCompletionStatus(int $progressPercent): string
    {
        if ($progressPercent >= 100) {
            return 'Lengkap';
        }
        if ($progressPercent <= 0) {
            return 'Belum Isi';
        }

        return 'Kurang';
    }

    private function genderGroupLabel(?string $gender): string
    {
        return match ($gender) {
            'male' => 'Ikhwan',
            'female' => 'Akhwat',
            default => '-',
        };
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

    public function scanPage()
    {
        return view('admin.registrations.scan');
    }

    public function edit(Request $request, Registration $registration)
    {
        $step = (int) $request->query('step', 1);

        $registration->load([
            'period',
            'documents' => fn($q) => $q->orderBy('type'),
            'studentProfile',
            'parentProfile',
            'statement',
        ]);

        $viewData = $this->adminWizardViewData($registration, $step);

        if ($step === 2) {
            return view('app.psb.wizard.step2', $viewData);
        }

        if ($step === 3) {
            return view('app.psb.wizard.step3', $viewData);
        }

        return view('app.psb.wizard.step1', $viewData);
    }

    public function saveStep1(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'funding_type' => ['required', 'in:mandiri,beasiswa'],
            'education_level' => ['required', 'in:SMP_NEW,SMA_NEW,SMA_OLD'],
            'period_wave' => ['required', 'integer', 'min:1'],
        ], [
            'funding_type.required' => 'Jenis pembiayaan wajib dipilih.',
            'education_level.required' => 'Jenjang pendidikan wajib dipilih.',
        ]);

        $fileRules = [
            'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'kk' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'birth_cert' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'ktp_father' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'ktp_mother' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'sktm' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'good_behavior' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ];
        $request->validate($fileRules);

        DB::transaction(function () use ($request, $registration, $validated) {
            $registration->update([
                'funding_type' => $validated['funding_type'],
                'education_level' => $validated['education_level'],
            ]);

            $this->setDocumentRequired($registration, 'SKTM', false);
            $this->setDocumentRequired($registration, 'GOOD_BEHAVIOR', false);

            $map = [
                'payment_proof' => 'PAYMENT_PROOF',
                'kk' => 'KK',
                'birth_cert' => 'BIRTH_CERT',
                'ktp_father' => 'KTP_FATHER',
                'ktp_mother' => 'KTP_MOTHER',
                'sktm' => 'SKTM',
                'good_behavior' => 'GOOD_BEHAVIOR',
            ];

            foreach ($map as $inputName => $docType) {
                if ($request->hasFile($inputName)) {
                    $this->storeDocumentFile($registration, $docType, $request->file($inputName));
                }
            }
        });

        return redirect()
            ->route('admin.registrations.edit', ['registration' => $registration, 'step' => 2])
            ->with('success', 'Step 1 berhasil disimpan. Lanjutkan ke Step 2.');
    }

    public function saveStep2(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'nisn' => ['nullable', 'string', 'max:30'],
            'nik' => ['required', 'string', 'max:30'],
            'birth_place' => ['required', 'string', 'max:120'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'address' => ['required', 'string', 'max:1000'],
            'province' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'district' => ['required', 'string', 'max:120'],
            'village' => ['required', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'school_origin' => ['required', 'string', 'max:255'],
            'hobby' => ['required', 'string', 'max:120'],
            'ambition' => ['required', 'string', 'max:120'],
            'religion' => ['required', 'string', 'max:50'],
            'nationality' => ['required', 'string', 'max:60'],
            'siblings_count' => ['nullable', 'integer', 'min:0', 'max:30'],
            'child_number' => ['nullable', 'integer', 'min:1', 'max:30'],
            'orphan_status' => ['required', Rule::in(['both', 'yatim', 'piatu', 'yatim_piatu'])],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'medical_history' => ['required', 'string', 'max:255'],
            'motivation' => ['required', Rule::in(['self', 'parents'])],
            'quran_memorization_level' => ['required', Rule::in(['lt_half', 'lt_one', 'ge_one', 'ge_three', 'ge_five'])],
            'quran_reading_level' => ['required', Rule::in(['none', 'iqro', 'fluent', 'fluent_tahsin'])],
            'program_choice' => ['required', Rule::in(['mahad', 'takhosus'])],
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi sesuai KK.',
            'birth_place.required' => 'Tempat lahir wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
        ]);

        $registration->update(['gender' => $validated['gender']]);

        StudentProfile::updateOrCreate(
            ['registration_id' => $registration->id],
            $validated
        );

        return redirect()
            ->route('admin.registrations.edit', ['registration' => $registration, 'step' => 3])
            ->with('success', 'Step 2 berhasil disimpan. Lanjutkan ke Step 3.');
    }

    public function saveStep3(Request $request, Registration $registration)
    {
        if (!$registration->studentProfile) {
            return redirect()
                ->route('admin.registrations.edit', ['registration' => $registration, 'step' => 2])
                ->withErrors(['step2' => 'Lengkapi dulu data calon santri di Step 2 sebelum menyimpan Step 3.']);
        }

        $validatedParent = $request->validate([
            'kk_number' => ['required', 'string', 'max:50'],
            'father_name' => ['required', 'string', 'max:255'],
            'father_nik' => ['required', 'string', 'max:30'],
            'father_birth_place' => ['required', 'string', 'max:120'],
            'father_birth_date' => ['required', 'date'],
            'father_religion' => ['required', 'string', 'max:50'],
            'father_education' => ['required', 'string', 'max:80'],
            'father_job' => ['required', 'string', 'max:120'],
            'father_income' => ['required', 'string', 'max:120'],
            'father_address' => ['required', 'string', 'max:1000'],
            'father_province' => ['required', 'string', 'max:120'],
            'father_city' => ['required', 'string', 'max:120'],
            'father_district' => ['required', 'string', 'max:120'],
            'father_village' => ['required', 'string', 'max:120'],
            'father_postal_code' => ['nullable', 'string', 'max:10'],
            'father_phone' => ['required', 'string', 'max:30'],
            'mother_name' => ['required', 'string', 'max:255'],
            'mother_nik' => ['required', 'string', 'max:30'],
            'mother_birth_place' => ['required', 'string', 'max:120'],
            'mother_birth_date' => ['required', 'date'],
            'mother_religion' => ['required', 'string', 'max:50'],
            'mother_education' => ['required', 'string', 'max:80'],
            'mother_job' => ['required', 'string', 'max:120'],
            'mother_income' => ['required', 'string', 'max:120'],
            'mother_phone' => ['required', 'string', 'max:30'],
            'favorite_ustadz' => ['required', 'string', 'max:120'],
        ]);

        $validatedStatement = $request->validate([
            'willing_to_serve' => ['required', Rule::in(['yes', 'no'])],
            'agree_morality' => ['required', Rule::in(['yes', 'no'])],
            'agree_rules' => ['required', Rule::in(['yes', 'no'])],
            'agree_integrity' => ['required', Rule::in(['yes', 'no'])],
            'agree_payment' => ['required', Rule::in(['yes', 'no'])],
        ]);

        if ($validatedStatement['willing_to_serve'] === 'no') {
            return back()->withErrors([
                'willing_to_serve' => 'Jika tidak bersedia mengabdi, pendaftaran tidak akan diproses.',
            ])->withInput();
        }

        foreach (['agree_morality', 'agree_rules', 'agree_integrity', 'agree_payment'] as $field) {
            if ($validatedStatement[$field] !== 'yes') {
                return back()->withErrors([
                    $field => 'Pernyataan ini harus disetujui.',
                ])->withInput();
            }
        }

        DB::transaction(function () use ($registration, $validatedParent) {
            ParentProfile::updateOrCreate(
                ['registration_id' => $registration->id],
                $validatedParent
            );

            $statementData = [
                'willing_to_serve' => true,
                'agree_morality' => true,
                'agree_rules' => true,
                'agree_payment' => true,
                'submitted_at' => now(),
            ];

            if (Schema::hasColumn('statements', 'agree_integrity')) {
                $statementData['agree_integrity'] = true;
            }

            Statement::updateOrCreate(
                ['registration_id' => $registration->id],
                $statementData
            );
        });

        return redirect()
            ->route('admin.registrations.show', $registration)
            ->with('success', 'Data pendaftaran berhasil diperbarui.');
    }

    public function destroy(Registration $registration)
    {
        $filePaths = $registration->documents()
            ->whereNotNull('file_path')
            ->pluck('file_path')
            ->toArray();

        DB::transaction(function () use ($registration) {
            $registration->delete();
        });

        foreach ($filePaths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        return back()->with('success', 'Data pendaftaran berhasil dihapus.');
    }

    private function setDocumentRequired(Registration $registration, string $type, bool $required): void
    {
        Document::query()
            ->where('registration_id', $registration->id)
            ->where('type', $type)
            ->update(['is_required' => $required]);
    }

    private function storeDocumentFile(Registration $registration, string $type, \Illuminate\Http\UploadedFile $file): void
    {
        $doc = Document::query()
            ->where('registration_id', $registration->id)
            ->where('type', $type)
            ->firstOrFail();

        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $folder = "psb/{$registration->registration_no}";
        $storedPath = $file->store($folder, 'public');

        $doc->update([
            'file_path' => $storedPath,
            'is_verified' => false,
            'note' => null,
        ]);
    }

    private function adminWizardViewData(Registration $registration, int $step): array
    {
        return [
            'registration' => $registration,
            'activePeriod' => $registration->period,
            'step' => $step,
            'wizardMode' => 'admin',
            'wizardTitle' => 'Edit Pendaftaran',
            'step1Action' => route('admin.registrations.step1', $registration),
            'step2Action' => route('admin.registrations.step2', $registration),
            'step3Action' => route('admin.registrations.step3', $registration),
            'step1Url' => route('admin.registrations.edit', ['registration' => $registration, 'step' => 1]),
            'step2Url' => route('admin.registrations.edit', ['registration' => $registration, 'step' => 2]),
            'step3Url' => route('admin.registrations.edit', ['registration' => $registration, 'step' => 3]),
            'listUrl' => route('admin.registrations.index'),
            'detailUrl' => route('admin.registrations.show', $registration),
            'deleteUrl' => route('admin.registrations.destroy', $registration),
            'showDeleteButton' => auth()->user()?->role === 'admin',
            'step3SubmitLabel' => 'Simpan Perubahan',
            'wilayahOptionsUrl' => route('admin.wilayah.options'),
        ];
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
