<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Period;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\StudentProfile;
use App\Models\ParentProfile;
use App\Models\Statement;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PsbWizardController extends Controller
{
    public function dashboard(Request $request)
    {
        $activePeriod = \App\Models\Period::query()->active()->latest('id')->first();

        $registration = \App\Models\Registration::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->with(['period', 'documents', 'studentProfile', 'parentProfile', 'statement'])
            ->first();

        // Jika belum ada pendaftaran sama sekali: arahkan ke wizard step 1 (auto create di show())
        if (!$registration) {
            return redirect()->route('psb.wizard', ['step' => 1]);
        }

        // Hitung progress
        $step1Complete = $registration->isStep1Complete();
        $step2Complete = (bool) $registration->studentProfile;
        $step3Complete = (bool) $registration->parentProfile && (bool) $registration->statement;

        $stepsDone = collect([$step1Complete, $step2Complete, $step3Complete])->filter()->count();
        $progressPercent = (int) round(($stepsDone / 3) * 100);

        // Next step
        $nextStep = 1;
        if ($step1Complete)
            $nextStep = 2;
        if ($step1Complete && $step2Complete)
            $nextStep = 3;
        if ($step1Complete && $step2Complete && $step3Complete)
            $nextStep = 3; // sudah lengkap, step 3 jadi review

        // WA group berdasarkan gender
        $waLink = null;
        if ($registration->gender === 'male') {
            $waLink = $registration->period?->wa_group_ikhwan ?? $activePeriod?->wa_group_ikhwan;
        } elseif ($registration->gender === 'female') {
            $waLink = $registration->period?->wa_group_akhwat ?? $activePeriod?->wa_group_akhwat;
        }

        // Missing docs list (untuk alert)
        $missingDocs = $registration->missingRequiredDocuments();
        // dd($waLink);
        return view('app.dashboard', compact(
            'registration',
            'activePeriod',
            'step1Complete',
            'step2Complete',
            'step3Complete',
            'progressPercent',
            'nextStep',
            'waLink',
            'missingDocs'
        ));
    }

    public function result(Request $request)
    {
        $activePeriod = \App\Models\Period::query()->active()->latest('id')->first();

        $registration = \App\Models\Registration::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->with(['period', 'documents', 'studentProfile', 'parentProfile', 'statement'])
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

        // Ambil atau buat draft registration untuk user ini
        $registration = Registration::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->first();

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

        // Load documents for UI
        $registration->load('documents');
        $registration->load(['documents', 'studentProfile', 'parentProfile', 'statement', 'period']);

        if ($step === 2) {
            return view('app.psb.wizard.step2', compact('registration', 'activePeriod', 'step'));
        }
        if ($step === 3) {
            return view('app.psb.wizard.step3', compact('registration', 'activePeriod', 'step'));
        }
        return view('app.psb.wizard.step1', compact('registration', 'activePeriod', 'step'));

    }

    public function saveStep1(Request $request)
    {
        $user = $request->user();

        /** @var Registration $registration */
        $registration = Registration::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->firstOrFail();

        // Validasi pilihan program
        $validated = $request->validate([
            'funding_type' => ['required', 'in:mandiri,beasiswa'],
            'education_level' => ['required', 'in:SMP_NEW,SMA_NEW,SMA_OLD'],
            'period_wave' => ['required', 'integer', 'min:1'], // sementara wave saja
        ], [
            'funding_type.required' => 'Jenis pembiayaan wajib dipilih.',
            'education_level.required' => 'Jenjang pendidikan wajib dipilih.',
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

            // Update required docs berdasarkan kondisi
            // SKTM required jika beasiswa (tapi boleh menyusul: kita tandai required=true, tapi verifikasi admin nanti)
            $this->setDocumentRequired($registration, 'SKTM', $registration->funding_type === 'beasiswa');

            // Good behavior required jika SMA_NEW (dari luar Darussalam) - interpretasi paling aman untuk MVP
            $this->setDocumentRequired($registration, 'GOOD_BEHAVIOR', $registration->education_level === 'SMA_NEW');

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

        // Redirect ke step 2 nanti (belum kita buat sekarang)
        return redirect()->route('psb.wizard', ['step' => 2])
            ->with('success', 'Step 1 berhasil disimpan. Lanjutkan ke Step 2 (Data Calon Santri).');
    }
    public function saveStep2(Request $request)
    {
        $registration = Registration::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->firstOrFail();

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

        // Simpan gender juga ke registrations (dipakai untuk WA group)
        $registration->update(['gender' => $validated['gender']]);

        StudentProfile::updateOrCreate(
            ['registration_id' => $registration->id],
            $validated
        );

        return redirect()->route('psb.wizard', ['step' => 3])
            ->with('success', 'Step 2 berhasil disimpan. Lanjutkan ke Step 3 (Orang Tua + Pernyataan).');
    }
    public function saveStep3Submit(Request $request)
    {
        $registration = Registration::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->with('documents', 'studentProfile')
            ->firstOrFail();

        // Guard: Step 1 minimal lengkap dokumen wajib
        $missing = $registration->missingRequiredDocuments();
        if (!empty($missing)) {
            return redirect()->route('psb.wizard', ['step' => 1])
                ->with('success', 'Lengkapi dulu dokumen wajib di Step 1 sebelum submit final.');
        }

        // Guard: Step 2 harus ada
        if (!$registration->studentProfile) {
            return redirect()->route('psb.wizard', ['step' => 2])
                ->with('success', 'Lengkapi dulu data calon santri di Step 2 sebelum submit final.');
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
            'father_city' => ['required', 'string', 'max:120'],
            'father_district' => ['required', 'string', 'max:120'],
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

            Statement::updateOrCreate(
                ['registration_id' => $registration->id],
                [
                    'willing_to_serve' => true,
                    'agree_morality' => true,
                    'agree_rules' => true,
                    'agree_payment' => true,
                    'submitted_at' => now(),
                ]
            );

            // Submit final
            $registration->update([
                'status' => 'submitted',
            ]);
        });

        // Setelah submit: arahkan ke halaman selesai / dashboard dengan link group WA
        return redirect()->route('app.dashboard')
            ->with('success', 'Pendaftaran berhasil disubmit. Silakan bergabung ke grup calon peserta ujian.');
    }


    private function generateRegistrationNo(): string
    {
        // contoh: DS-2026-ABCDEFG
        $year = date('Y');
        return 'DS-' . $year . '-' . strtoupper(Str::random(7));
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
}

