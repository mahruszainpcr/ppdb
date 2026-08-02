<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminDocument;
use App\Models\Period;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDocumentController extends Controller
{
    public function index()
    {
        $documents = AdminDocument::query()
            ->with('uploader')
            ->latest('updated_at')
            ->latest('id')
            ->get();

        $activePeriod = Period::query()->active()->latest('id')->first();
        $registrations = Registration::query()
            ->with(['studentProfile', 'santriContinuation', 'documents'])
            ->when($activePeriod, fn($q) => $q->where('period_id', $activePeriod->id))
            ->latest('id')
            ->get();

        $registrationSummary = $this->buildRegistrationSummary($registrations, $activePeriod);

        return view('admin.documents.index', [
            'documents' => $documents,
            'registrationSummary' => $registrationSummary,
        ]);
    }

    public function create()
    {
        return view('admin.documents.form', [
            'document' => new AdminDocument(),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request, true);
        $file = $request->file('file');

        $document = AdminDocument::create([
            'category' => $data['category'],
            'custom_category' => $data['custom_category'],
            'description' => $data['description'] ?? null,
            'file_path' => $file->store('admin-documents', 'public'),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'uploaded_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('admin.documents.show', $document)
            ->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function show(AdminDocument $document)
    {
        $document->load('uploader');

        return view('admin.documents.show', [
            'document' => $document,
        ]);
    }

    public function edit(AdminDocument $document)
    {
        $document->load('uploader');

        return view('admin.documents.form', [
            'document' => $document,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(Request $request, AdminDocument $document)
    {
        $data = $this->validatedData($request, false);
        $payload = [
            'category' => $data['category'],
            'custom_category' => $data['custom_category'],
            'description' => $data['description'] ?? null,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $payload['file_path'] = $file->store('admin-documents', 'public');
            $payload['original_name'] = $file->getClientOriginalName();
            $payload['mime_type'] = $file->getClientMimeType();
            $payload['uploaded_by'] = $request->user()?->id;
        }

        $document->update($payload);

        return redirect()
            ->route('admin.documents.show', $document)
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(AdminDocument $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    public function preview(AdminDocument $document): StreamedResponse
    {
        abort_unless($document->file_path && Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->response(
            $document->file_path,
            $document->original_name,
            [
                'Content-Type' => $document->mime_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . addslashes($document->original_name) . '"',
            ]
        );
    }

    public function download(AdminDocument $document): StreamedResponse
    {
        abort_unless($document->file_path && Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_name,
            [
                'Content-Type' => $document->mime_type ?: 'application/pdf',
            ]
        );
    }

    private function validatedData(Request $request, bool $isCreate): array
    {
        $allowedCategories = collect(array_keys(AdminDocument::CATEGORY_OPTIONS))
            ->merge(AdminDocument::customCategoryOptions())
            ->unique()
            ->values()
            ->all();

        $rules = [
            'category' => ['required', 'string', 'max:120', Rule::in($allowedCategories)],
            'custom_category' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:3000'],
            'file' => [$isCreate ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];

        $data = $request->validate($rules);

        $data = $this->normalizeCategorySelection($data);

        if (($data['category'] ?? null) === 'lainnya') {
            validator(
                ['custom_category' => $data['custom_category'] ?? null],
                ['custom_category' => ['required', 'string', 'max:120']]
            )->validate();
        }

        return $data;
    }

    private function normalizeCategorySelection(array $data): array
    {
        $selectedCategory = trim((string) ($data['category'] ?? ''));

        if ($selectedCategory !== 'lainnya' && !array_key_exists($selectedCategory, AdminDocument::CATEGORY_OPTIONS)) {
            $data['category'] = 'lainnya';
            $data['custom_category'] = $selectedCategory;

            return $data;
        }

        $data['category'] = $selectedCategory;
        $data['custom_category'] = $selectedCategory === 'lainnya'
            ? trim((string) ($data['custom_category'] ?? ''))
            : null;

        return $data;
    }

    private function categoryOptions(): array
    {
        $customCategories = AdminDocument::customCategoryOptions()
            ->mapWithKeys(fn(string $category) => [$category => $category])
            ->all();

        return array_merge(
            AdminDocument::CATEGORY_OPTIONS,
            $customCategories,
        );
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
