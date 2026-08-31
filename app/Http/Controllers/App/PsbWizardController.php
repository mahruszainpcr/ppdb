<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Period;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\StudentProfile;
use App\Models\ParentProfile;
use App\Models\Statement;
use App\Models\SantriContinuation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PsbWizardController extends Controller
{
    public function dashboard(Request $request)
    {
        $activePeriod = \App\Models\Period::query()->active()->latest('id')->first();

        $registrations = \App\Models\Registration::query()
            ->where('user_id', $request->user()->id)
            ->with(['period', 'documents', 'studentProfile', 'parentProfile', 'statement', 'santriContinuation'])
            ->latest('id')
            ->get();

        // Jika belum ada pendaftaran sama sekali: arahkan ke wizard step 1 (auto create di show())
        if ($registrations->isEmpty()) {
            return redirect()->route('psb.create.choice');
        }

        // optional: pilih registration dari query (?registration=ID)
        $selectedRegistrationId = (int) $request->query('registration', 0);
        if ($selectedRegistrationId > 0 && $registrations->firstWhere('id', $selectedRegistrationId)) {
            $request->session()->put('active_registration_id', $selectedRegistrationId);
        }

        $activeRegistrationId = (int) $request->session()->get('active_registration_id', 0);
        $registration = $registrations->firstWhere('id', $activeRegistrationId) ?? $registrations->first();
        $request->session()->put('active_registration_id', $registration->id);

        // Hitung progress
        [
            'step1Complete' => $step1Complete,
            'step2Complete' => $step2Complete,
            'step3Complete' => $step3Complete,
            'progressPercent' => $progressPercent,
            'nextStep' => $nextStep,
            'continueUrl' => $continueUrl,
        ] = $this->registrationProgressSnapshot($registration);

        // WA group berdasarkan gender, hanya tampil jika progress sudah 100% dan periode belum ditutup
        $waLink = null;
        $showWaGroup = $progressPercent === 100 && !($activePeriod?->isRegistrationClosed() ?? false);
        if ($showWaGroup) {
            if ($registration->gender === 'male') {
                $waLink = $registration->period?->wa_group_ikhwan ?? $activePeriod?->wa_group_ikhwan;
            } elseif ($registration->gender === 'female') {
                $waLink = $registration->period?->wa_group_akhwat ?? $activePeriod?->wa_group_akhwat;
            }
        }

        // Missing docs list (untuk alert)
        $missingDocs = $registration->missingRequiredDocuments();
        $canShowActiveQr = $progressPercent === 100;
        $activeScanUrl = $canShowActiveQr ? $registration->admin_scan_url : null;
        $activeQrPageUrl = $canShowActiveQr ? $registration->parent_qr_url : null;
        $activeProofPdfUrl = $canShowActiveQr ? route('psb.proof.pdf', $registration) : null;

        $registrationHistories = $registrations->map(function (Registration $reg) {
            $snapshot = $this->registrationProgressSnapshot($reg);
            $canShowQr = $snapshot['progressPercent'] === 100;

            return [
                'id' => $reg->id,
                'registration_no' => $reg->registration_no,
                'student_name' => $reg->studentProfile?->full_name ?? $reg->santriContinuation?->full_name ?? '-',
                'status' => $reg->status,
                'progress' => $snapshot['progressPercent'],
                'next_step' => $snapshot['nextStep'],
                'continue_url' => $snapshot['continueUrl'],
                'created_at' => $reg->created_at?->format('d M Y') ?? '-',
                'scan_url' => $canShowQr ? $reg->admin_scan_url : null,
                'qr_page_url' => $canShowQr ? $reg->parent_qr_url : null,
                'proof_pdf_url' => $canShowQr ? route('psb.proof.pdf', $reg) : null,
                'can_show_qr' => $canShowQr,
            ];
        });

        // dd($waLink);
        return view('app.dashboard', compact(
            'registration',
            'activePeriod',
            'step1Complete',
            'step2Complete',
            'step3Complete',
            'progressPercent',
            'nextStep',
            'continueUrl',
            'waLink',
            'missingDocs',
            'canShowActiveQr',
            'activeScanUrl',
            'activeQrPageUrl',
            'activeProofPdfUrl',
            'registrationHistories',
            'activeRegistrationId'
        ));
    }

    public function showQr(Request $request, Registration $registration)
    {
        if ($registration->user_id !== $request->user()->id) {
            abort(403);
        }

        $registration->load(['period', 'studentProfile', 'parentProfile', 'statement', 'santriContinuation']);

        if (!$this->registrationProgressIsComplete($registration)) {
            return redirect()
                ->route('app.dashboard', ['registration' => $registration->id])
                ->withErrors(['qr' => 'QR bukti pendaftaran hanya tersedia setelah pendaftaran 100% lengkap.']);
        }

        return view('app.psb.qr', [
            'registration' => $registration,
            'scanUrl' => $registration->admin_scan_url,
        ]);
    }

    public function downloadProofPdf(Request $request, Registration $registration)
    {
        if ($registration->user_id !== $request->user()->id) {
            abort(403);
        }

        $registration->load(['period', 'studentProfile', 'parentProfile', 'statement', 'santriContinuation']);

        if (!$this->registrationProgressIsComplete($registration)) {
            return redirect()
                ->route('app.dashboard', ['registration' => $registration->id])
                ->withErrors(['qr' => 'PDF bukti pendaftaran hanya tersedia setelah pendaftaran 100% lengkap.']);
        }

        $view = $registration->education_level === 'SMA_OLD'
            ? 'pdf.santri-continuation-letter'
            : 'pdf.registration-proof';

        $data = $registration->education_level === 'SMA_OLD'
            ? $this->santriContinuationLetterViewData($registration)
            : $this->registrationProofViewData($registration);

        return Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->download('bukti-pendaftaran-' . $registration->registration_no . '.pdf');
    }

    public function createNew(Request $request)
    {
        $activePeriod = Period::query()->where('is_active', true)->latest('id')->first();

        $registration = Registration::create([
            'user_id' => $request->user()->id,
            'period_id' => $activePeriod?->id,
            'registration_no' => $this->generateRegistrationNo(),
            'funding_type' => 'mandiri',
            'education_level' => 'SMP_NEW',
            'status' => 'draft',
            'graduation_status' => 'pending',
        ]);

        $this->seedDefaultDocuments($registration);
        $request->session()->put('active_registration_id', $registration->id);

        return redirect()->route('psb.wizard', ['step' => 1])
            ->with('success', 'Draft pendaftaran baru berhasil dibuat. Silakan lengkapi data calon santri.');
    }

    public function createChoice()
    {
        return view('app.psb.create-choice');
    }

    public function createContinuation(Request $request)
    {
        $activePeriod = Period::query()->where('is_active', true)->latest('id')->first();

        $registration = Registration::create([
            'user_id' => $request->user()->id,
            'period_id' => $activePeriod?->id,
            'registration_no' => $this->generateRegistrationNo(),
            'funding_type' => 'mandiri',
            'education_level' => 'SMA_OLD',
            'status' => 'draft',
            'graduation_status' => 'pending',
        ]);

        $request->session()->put('active_registration_id', $registration->id);

        return redirect()
            ->route('psb.continuation.form', $registration)
            ->with('success', 'Formulir lanjutan santri berhasil dibuat. Silakan lengkapi data santri lama.');
    }

    public function destroyRegistration(Request $request, Registration $registration)
    {
        if ($registration->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($registration->status !== 'draft') {
            return back()->withErrors(['delete' => 'Hanya pendaftaran berstatus draft yang bisa dihapus.']);
        }

        $filePaths = $registration->documents()
            ->whereNotNull('file_path')
            ->pluck('file_path')
            ->toArray();

        if ($registration->santriContinuation?->signature_path) {
            $filePaths[] = $registration->santriContinuation->signature_path;
        }
        if ($registration->santriContinuation?->payment_proof_path) {
            $filePaths[] = $registration->santriContinuation->payment_proof_path;
        }

        DB::transaction(function () use ($registration) {
            $registration->delete();
        });

        foreach ($filePaths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $activeRegistrationId = (int) $request->session()->get('active_registration_id', 0);
        if ($activeRegistrationId === $registration->id) {
            $replacement = Registration::query()
                ->where('user_id', $request->user()->id)
                ->latest('id')
                ->first();

            if ($replacement) {
                $request->session()->put('active_registration_id', $replacement->id);
            } else {
                $request->session()->forget('active_registration_id');
            }
        }

        return back()->with('success', 'Pendaftaran berhasil dihapus.');
    }

    public function result(Request $request)
    {
        $activePeriod = \App\Models\Period::query()->active()->latest('id')->first();

        $registration = \App\Models\Registration::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->with(['period', 'documents', 'studentProfile', 'parentProfile', 'statement', 'santriContinuation'])
            ->first();

        if (!$registration) {
            return redirect()->route('psb.wizard', ['step' => 1]);
        }

        $period = $registration->period ?? $activePeriod;

        return view('app.result', compact('registration', 'activePeriod', 'period'));
    }

    public function show(Request $request)
    {
        $step = (int) $request->query('step', 1);

        // Ambil periode aktif (opsional: kalau belum ada, tetap bisa isi; nanti admin set)
        $activePeriod = Period::query()->where('is_active', true)->latest('id')->first();

        $selectedRegistrationId = (int) $request->query('registration', 0);
        if ($selectedRegistrationId > 0) {
            $selectedRegistration = Registration::query()
                ->where('user_id', $request->user()->id)
                ->where('id', $selectedRegistrationId)
                ->first();

            if ($selectedRegistration) {
                $request->session()->put('active_registration_id', $selectedRegistration->id);
            }
        }

        // Ambil atau buat draft registration untuk user ini
        $registration = Registration::query()
            ->where('user_id', $request->user()->id)
            ->where('id', (int) $request->session()->get('active_registration_id', 0))
            ->first();

        if (!$registration) {
            $registration = Registration::query()
                ->where('user_id', $request->user()->id)
                ->latest('id')
                ->first();
        }

        if (!$registration) {
            $registration = Registration::create([
                'user_id' => $request->user()->id,
                'period_id' => $activePeriod?->id,
                'registration_no' => $this->generateRegistrationNo(),
                'funding_type' => 'mandiri',
                'education_level' => 'SMP_NEW',
                'status' => 'draft',
                'graduation_status' => 'pending',
            ]);

            $this->seedDefaultDocuments($registration);
        } else {
            // pastikan dokumen default ada (kalau migration lama / data lama)
            $this->ensureDefaultDocumentsExist($registration);
        }
        $request->session()->put('active_registration_id', $registration->id);

        // Load documents for UI
        $registration->load('documents');
        $registration->load(['documents', 'studentProfile', 'parentProfile', 'statement', 'period', 'santriContinuation']);

        if ($registration->education_level === 'SMA_OLD' && $step > 1) {
            return redirect()->route('psb.continuation.form', $registration);
        }

        if ($step === 1) {
            return view('app.psb.wizard.step2', [
                'registration' => $registration,
                'activePeriod' => $activePeriod,
                'schoolOriginOptions' => StudentProfile::schoolOriginOptions(),
                'step' => 1,
                'wizardStepNumber' => 1,
                'wizardStepTitle' => 'Step 1',
                'step1Status' => 'active',
                'step2Status' => (bool) $registration->parentProfile && (bool) $registration->statement ? 'done' : 'upcoming',
                'step3Status' => $registration->isStep1Complete() ? 'done' : 'upcoming',
            ]);
        }
        if ($step === 2) {
            return view('app.psb.wizard.step3', [
                'registration' => $registration,
                'activePeriod' => $activePeriod,
                'step' => 2,
                'wizardStepNumber' => 2,
                'wizardStepTitle' => 'Step 2',
                'step2Url' => route('psb.wizard', ['step' => 1, 'registration' => $registration->id]),
                'step3SubmitLabel' => 'Simpan & Lanjut Step 3',
                'step1Status' => (bool) $registration->studentProfile ? 'done' : 'active',
                'step2Status' => 'active',
                'step3Status' => $registration->isStep1Complete() ? 'done' : 'upcoming',
            ]);
        }
        return view('app.psb.wizard.step1', [
            'registration' => $registration,
            'activePeriod' => $activePeriod,
            'step' => 3,
            'wizardStepNumber' => 3,
            'wizardStepTitle' => 'Step 3',
            'step1Status' => (bool) $registration->studentProfile ? 'done' : 'upcoming',
            'step2Status' => (bool) $registration->parentProfile && (bool) $registration->statement ? 'done' : 'upcoming',
            'step3Status' => 'active',
        ]);

    }

    public function saveStep1(Request $request)
    {
        $user = $request->user();

        /** @var Registration $registration */
        $registration = Registration::query()
            ->where('user_id', $user->id)
            ->where('id', (int) $request->session()->get('active_registration_id', 0))
            ->first();
        if (!$registration) {
            $registration = Registration::query()
                ->where('user_id', $user->id)
                ->latest('id')
                ->firstOrFail();
            $request->session()->put('active_registration_id', $registration->id);
        }

        $activePeriod = Period::query()->where('is_active', true)->latest('id')->first();
        $isScholarshipAllowed = (int) ($activePeriod?->wave ?? 1) === 1;

        // Validasi pilihan program
        $validated = $request->validate([
            'funding_type' => [
                'required',
                'in:mandiri,beasiswa',
                Rule::when(!$isScholarshipAllowed, 'in:mandiri'),
            ],
            'education_level' => ['required', 'in:SMP_NEW,SMA_NEW,SMA_OLD'],
            'period_wave' => ['required', 'integer', 'min:1'], // sementara wave saja
        ], [
            'funding_type.required' => 'Jenis pembiayaan wajib dipilih.',
            'education_level.required' => 'Jenjang pendidikan wajib dipilih.',
            'funding_type.in' => $isScholarshipAllowed
                ? 'Jenis pembiayaan yang dipilih tidak valid.'
                : 'Jalur beasiswa hanya tersedia untuk periode 1.',
        ]);

        // Validasi file upload (praktik aman 10MB)
        // (Kalau kamu bener-bener butuh >10MB, bilang nanti, kita arahkan ke S3/R2)
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

            // Simpan pilihan step 1
            $registration->update([
                'funding_type' => $validated['funding_type'],
                'education_level' => $validated['education_level'],
                // period_id tetap dari periode aktif (bisa disesuaikan: pilih gelombang -> period_id)
            ]);

            // Dokumen ini tetap opsional dan boleh menyusul.
            $this->setDocumentRequired($registration, 'SKTM', false);
            $this->setDocumentRequired($registration, 'GOOD_BEHAVIOR', false);

            // Upload mapping
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

        if ($registration->education_level === 'SMA_OLD') {
            return redirect()
                ->route('psb.continuation.form', $registration)
                ->with('success', 'Pilihan program berhasil disimpan. Silakan lengkapi formulir lanjutan santri.');
        }

        if ($registration->studentProfile && $registration->parentProfile && $registration->statement) {
            $registration->update(['status' => 'submitted']);

            return redirect()->route('app.dashboard', ['registration' => $registration->id])
                ->with('success', 'Dokumen berhasil disimpan dan pendaftaran selesai disubmit.');
        }

        if (!$registration->studentProfile) {
            return redirect()->route('psb.wizard', ['step' => 1])
                ->with('success', 'Program dan dokumen berhasil disimpan. Lanjutkan ke Step 1 (Data Santri).');
        }

        if (!$registration->parentProfile || !$registration->statement) {
            return redirect()->route('psb.wizard', ['step' => 2])
                ->with('success', 'Program dan dokumen berhasil disimpan. Lanjutkan ke Step 2 (Orang Tua & Pernyataan).');
        }

        return redirect()->route('psb.wizard', ['step' => 3])
            ->with('success', 'Program dan dokumen berhasil disimpan.');
    }
    public function saveStep2(Request $request)
    {
        $registration = Registration::query()
            ->where('user_id', $request->user()->id)
            ->where('id', (int) $request->session()->get('active_registration_id', 0))
            ->first();
        if (!$registration) {
            $registration = Registration::query()
                ->where('user_id', $request->user()->id)
                ->latest('id')
                ->firstOrFail();
            $request->session()->put('active_registration_id', $registration->id);
        }

        // Validasi sesuai form kamu (yang bertanda * wajib)
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
            'school_origin_custom' => ['nullable', 'string', 'max:255'],
            'hobby' => ['required', 'string', 'max:120'],
            'ambition' => ['required', 'string', 'max:120'],

            'religion' => ['required', 'string', 'max:50'],
            'nationality' => ['required', 'string', 'max:60'],

            'siblings_count' => ['nullable', 'integer', 'min:0', 'max:30'],
            'child_number' => ['nullable', 'integer', 'min:1', 'max:30'],

            'orphan_status' => ['required', Rule::in(['both', 'yatim', 'piatu', 'yatim_piatu'])],
            'blood_type' => ['nullable', 'string', 'max:10'],

            'medical_history' => ['required', 'string', 'max:255'], // "Tidak" jika tidak ada
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

        $validated['school_origin'] = $this->normalizeSchoolOrigin(
            $validated['school_origin'] ?? null,
            $validated['school_origin_custom'] ?? null,
        );
        unset($validated['school_origin_custom']);

        // Simpan gender juga ke registrations (dipakai untuk WA group)
        $registration->update(['gender' => $validated['gender']]);

        StudentProfile::updateOrCreate(
            ['registration_id' => $registration->id],
            $validated
        );

        return redirect()->route('psb.wizard', ['step' => 2])
            ->with('success', 'Step 1 berhasil disimpan. Lanjutkan ke Step 2 (Orang Tua & Pernyataan).');
    }
    public function saveStep3Submit(Request $request)
    {
        $registration = Registration::query()
            ->where('user_id', $request->user()->id)
            ->where('id', (int) $request->session()->get('active_registration_id', 0))
            ->with('documents', 'studentProfile')
            ->first();
        if (!$registration) {
            $registration = Registration::query()
                ->where('user_id', $request->user()->id)
                ->with('documents', 'studentProfile')
                ->latest('id')
                ->firstOrFail();
            $request->session()->put('active_registration_id', $registration->id);
        }

        // Guard: Step 1 harus ada
        if (!$registration->studentProfile) {
            return redirect()->route('psb.wizard', ['step' => 1])
                ->with('success', 'Lengkapi dulu data calon santri di Step 1 sebelum lanjut.');
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

        // Pernyataan
        // Pernyataan
        $validatedStatement = $request->validate([
            'willing_to_serve' => ['required', Rule::in(['yes', 'no'])],
            'agree_morality' => ['accepted'],
            'agree_rules' => ['accepted'],
            'agree_integrity' => ['accepted'],
            'agree_payment' => ['accepted'],
        ], [
            'willing_to_serve.required' => 'Pilih jawaban kesediaan mengabdi.',
            'agree_morality.accepted' => 'Anda harus menyetujui pernyataan akhlak & larangan.',
            'agree_rules.accepted' => 'Anda harus menyetujui tata tertib dan ketentuan ma’had.',
            'agree_integrity.accepted' => 'Anda harus menyetujui pernyataan menjaga nama baik dan menyelesaikan masalah secara kekeluargaan.',
            'agree_payment.accepted' => 'Anda harus menyetujui kesanggupan pembayaran.',
        ]);// Jika tidak bersedia mengabdi -> stop
        if ($validatedStatement['willing_to_serve'] === 'no') {
            return back()->withErrors([
                'willing_to_serve' => 'Jika tidak bersedia mengabdi, pendaftaran tidak akan diproses.',
            ])->withInput();
        }

        DB::transaction(function () use ($registration, $validatedParent, $validatedStatement) {

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

        return redirect()->route('psb.wizard', ['step' => 3])
            ->with('success', 'Step 2 berhasil disimpan. Lanjutkan ke Step 3 (Program & Dokumen).');
    }

    public function showContinuationForm(Request $request, Registration $registration)
    {
        if ($registration->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($registration->education_level !== 'SMA_OLD') {
            return redirect()->route('psb.wizard', ['step' => 2, 'registration' => $registration->id]);
        }

        $registration->load(['period', 'santriContinuation']);
        $request->session()->put('active_registration_id', $registration->id);

        return view('app.psb.continuation.form', $this->continuationFormViewData($registration));
    }

    public function saveContinuationForm(Request $request, Registration $registration)
    {
        if ($registration->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($registration->education_level !== 'SMA_OLD') {
            return redirect()->route('psb.wizard', ['step' => 2, 'registration' => $registration->id]);
        }

        $existingContinuation = $registration->santriContinuation;

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'last_class' => ['required', 'string', 'max:100'],
            'dormitory' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'father_phone' => ['required', 'string', 'max:30'],
            'mother_name' => ['required', 'string', 'max:255'],
            'mother_phone' => ['required', 'string', 'max:30'],
            'continue_to_ulya' => ['accepted'],
            'agree_rules' => ['accepted'],
            'agree_programs' => ['accepted'],
            'agree_administration' => ['accepted'],
            'bedding_option' => ['required', Rule::in(['buy', 'not_buy'])],
            'payment_proof' => [
                $existingContinuation?->payment_proof_path ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:10240'
            ],
            'signature_data' => ['required', 'string'],
        ], [
            'continue_to_ulya.accepted' => 'Pilih persetujuan melanjutkan pendidikan di Ulya.',
            'agree_rules.accepted' => 'Komitmen menaati peraturan wajib disetujui.',
            'agree_programs.accepted' => 'Komitmen mengikuti seluruh program wajib disetujui.',
            'agree_administration.accepted' => 'Komitmen administrasi wajib disetujui.',
            'payment_proof.required' => 'Bukti transfer pembayaran Rp150.000 wajib diunggah.',
            'signature_data.required' => 'Tanda tangan orang tua / wali wajib diisi.',
        ]);

        DB::transaction(function () use ($request, $registration, $validated, $existingContinuation) {
            $continuation = SantriContinuation::query()->firstOrNew([
                'registration_id' => $registration->id,
            ]);

            $signaturePath = $this->storeSignatureImage(
                $validated['signature_data'],
                $registration->registration_no,
                $continuation->signature_path
            );
            $paymentProofPath = $continuation->payment_proof_path;
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $this->storeContinuationPaymentProof(
                    $request->file('payment_proof'),
                    $registration->registration_no,
                    $continuation->payment_proof_path
                );
            }

            $continuation->fill([
                'full_name' => $validated['full_name'],
                'last_class' => $validated['last_class'],
                'dormitory' => $validated['dormitory'],
                'father_name' => $validated['father_name'],
                'father_phone' => $validated['father_phone'],
                'mother_name' => $validated['mother_name'],
                'mother_phone' => $validated['mother_phone'],
                'continue_to_ulya' => true,
                'agree_rules' => true,
                'agree_programs' => true,
                'agree_administration' => true,
                'bedding_option' => $validated['bedding_option'],
                'payment_proof_path' => $paymentProofPath,
                'signature_path' => $signaturePath,
                'submitted_at' => now(),
            ])->save();

            $registration->update([
                'gender' => $validated['gender'],
                'status' => 'submitted',
            ]);
        });

        $registration->refresh()->load(['period', 'santriContinuation']);

        return Pdf::loadView('pdf.santri-continuation-letter', $this->santriContinuationLetterViewData($registration))
            ->setPaper('a4', 'portrait')
            ->download('formulir-lanjutan-' . $registration->registration_no . '.pdf');
    }


    private function generateRegistrationNo(): string
    {
        $year = date('y');
        $nextId = ((int) Registration::query()->max('id')) + 1;

        return sprintf('DS-%s-%03d', $year, $nextId);
    }

    private function seedDefaultDocuments(Registration $registration): void
    {
        $defaults = [
            ['type' => 'PAYMENT_PROOF', 'is_required' => true],
            ['type' => 'KK', 'is_required' => true],
            ['type' => 'BIRTH_CERT', 'is_required' => true],
            ['type' => 'KTP_FATHER', 'is_required' => true],
            ['type' => 'KTP_MOTHER', 'is_required' => true],
            ['type' => 'SKTM', 'is_required' => false], // conditional
            ['type' => 'GOOD_BEHAVIOR', 'is_required' => false], // conditional
        ];

        foreach ($defaults as $d) {
            Document::create([
                'registration_id' => $registration->id,
                'type' => $d['type'],
                'is_required' => $d['is_required'],
                'is_verified' => false,
                'file_path' => null,
            ]);
        }
    }

    private function ensureDefaultDocumentsExist(Registration $registration): void
    {
        $types = ['PAYMENT_PROOF', 'KK', 'BIRTH_CERT', 'KTP_FATHER', 'KTP_MOTHER', 'SKTM', 'GOOD_BEHAVIOR'];

        foreach ($types as $type) {
            Document::firstOrCreate(
                ['registration_id' => $registration->id, 'type' => $type],
                ['is_required' => in_array($type, ['PAYMENT_PROOF', 'KK', 'BIRTH_CERT', 'KTP_FATHER', 'KTP_MOTHER']), 'is_verified' => false]
            );
        }
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

        // Hapus file lama jika ada
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

    private function registrationProofViewData(Registration $registration): array
    {
        $student = $registration->studentProfile;
        $scanUrl = $registration->admin_scan_url;
        $qrSvg = QrCode::format('svg')->size(280)->margin(1)->generate($scanUrl);

        return [
            'registration' => $registration,
            'student' => $student,
            'scanUrl' => $scanUrl,
            'qrImage' => 'data:image/svg+xml;base64,' . base64_encode($qrSvg),
            'logoImage' => $this->pdfLogoImage(),
            'downloadedAt' => now(),
        ];
    }

    private function registrationProgressIsComplete(Registration $registration): bool
    {
        if ($registration->education_level === 'SMA_OLD') {
            return (bool) $registration->santriContinuation?->payment_proof_path
                && (bool) $registration->santriContinuation?->signature_path
                && (bool) $registration->santriContinuation;
        }

        return $registration->isStep1Complete()
            && (bool) $registration->studentProfile
            && (bool) $registration->parentProfile
            && (bool) $registration->statement;
    }

    private function registrationProgressSnapshot(Registration $registration): array
    {
        if ($registration->education_level === 'SMA_OLD') {
            $complete = (bool) $registration->santriContinuation
                && (bool) $registration->santriContinuation?->payment_proof_path
                && (bool) $registration->santriContinuation?->signature_path;

            return [
                'step1Complete' => $complete,
                'step2Complete' => $complete,
                'step3Complete' => $complete,
                'progressPercent' => $complete ? 100 : 0,
                'nextStep' => 1,
                'continueUrl' => route('psb.continuation.form', $registration),
            ];
        }

        $step1Complete = (bool) $registration->studentProfile;
        $step2Complete = (bool) $registration->parentProfile && (bool) $registration->statement;
        $step3Complete = $registration->isStep1Complete();

        $stepsDone = collect([$step1Complete, $step2Complete, $step3Complete])->filter()->count();
        $progressPercent = (int) round(($stepsDone / 3) * 100);

        $nextStep = 1;
        if ($step1Complete) {
            $nextStep = 2;
        }
        if ($step1Complete && $step2Complete) {
            $nextStep = 3;
        }
        if ($step1Complete && $step2Complete && $step3Complete) {
            $nextStep = 3;
        }

        return [
            'step1Complete' => $step1Complete,
            'step2Complete' => $step2Complete,
            'step3Complete' => $step3Complete,
            'progressPercent' => $progressPercent,
            'nextStep' => $nextStep,
            'continueUrl' => route('psb.wizard', ['step' => $nextStep, 'registration' => $registration->id]),
        ];
    }

    private function continuationFormViewData(Registration $registration, array $overrides = []): array
    {
        return array_merge([
            'registration' => $registration,
            'activePeriod' => $registration->period,
            'continuation' => $registration->santriContinuation,
            'formAction' => route('psb.continuation.save', $registration),
            'backUrl' => route('app.dashboard', ['registration' => $registration->id]),
            'downloadUrl' => $registration->santriContinuation ? route('psb.proof.pdf', $registration) : null,
            'pageTitle' => 'Formulir Lanjutan Santri Ulya',
            'pageSubtitle' => 'Khusus SMA - Santri Lama (SMP di Darussalam)',
            'submitLabel' => 'Simpan & Download Surat',
            'isAdminMode' => false,
        ], $overrides);
    }

    private function normalizeSchoolOrigin(?string $selected, ?string $custom): string
    {
        $selected = trim((string) $selected);
        $custom = trim((string) $custom);

        if ($selected === 'lainnya') {
            return mb_strtoupper($custom);
        }

        return mb_strtoupper($selected);
    }

    private function storeSignatureImage(string $dataUrl, string $registrationNo, ?string $existingPath = null): string
    {
        if (!str_starts_with($dataUrl, 'data:image/png;base64,')) {
            abort(422, 'Format tanda tangan tidak valid.');
        }

        $binary = base64_decode(substr($dataUrl, strlen('data:image/png;base64,')), true);

        if ($binary === false) {
            abort(422, 'Tanda tangan tidak dapat diproses.');
        }

        if ($existingPath && Storage::disk('public')->exists($existingPath)) {
            Storage::disk('public')->delete($existingPath);
        }

        $path = 'psb-signatures/' . $registrationNo . '-continuation-signature.png';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    private function storeContinuationPaymentProof(\Illuminate\Http\UploadedFile $file, string $registrationNo, ?string $existingPath = null): string
    {
        if ($existingPath && Storage::disk('public')->exists($existingPath)) {
            Storage::disk('public')->delete($existingPath);
        }

        $extension = strtolower($file->getClientOriginalExtension()) ?: $file->extension() ?: 'jpg';
        $path = 'psb-continuation-payments/' . $registrationNo . '-payment-proof.' . $extension;

        Storage::disk('public')->putFileAs(
            'psb-continuation-payments',
            $file,
            basename($path)
        );

        return $path;
    }

    private function santriContinuationLetterViewData(Registration $registration): array
    {
        $continuation = $registration->santriContinuation;

        return [
            'registration' => $registration,
            'continuation' => $continuation,
            'logoImage' => $this->pdfLogoImage(),
            'signatureImage' => $continuation?->signature_path && Storage::disk('public')->exists($continuation->signature_path)
                ? 'data:image/png;base64,' . base64_encode(Storage::disk('public')->get($continuation->signature_path))
                : '',
            'downloadedAt' => now(),
        ];
    }

    private function pdfLogoImage(): string
    {
        $candidates = [
            public_path('logo.png'),
            public_path('assets/images/logo-icon.png'),
            public_path('assets/images/logo.svg'),
            public_path('assets/images/landing/logo.svg'),
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                $contents = file_get_contents($path);

                if ($contents !== false) {
                    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $mime = match ($extension) {
                        'svg' => 'image/svg+xml',
                        'jpg', 'jpeg' => 'image/jpeg',
                        default => 'image/png',
                    };

                    return 'data:' . $mime . ';base64,' . base64_encode($contents);
                }
            }
        }

        return '';
    }
}
