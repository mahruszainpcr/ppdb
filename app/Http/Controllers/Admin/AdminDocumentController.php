<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        return view('admin.documents.index', [
            'documents' => $documents,
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
            'custom_category' => $data['category'] === 'lainnya' ? $data['custom_category'] : null,
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
            'custom_category' => $data['category'] === 'lainnya' ? $data['custom_category'] : null,
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
        $rules = [
            'category' => ['required', 'string', 'max:120'],
            'custom_category' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:3000'],
            'file' => [$isCreate ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];

        $data = $request->validate($rules);
        $allowedCategories = collect(array_keys(AdminDocument::CATEGORY_OPTIONS))
            ->merge(AdminDocument::customCategoryOptions())
            ->all();

        $request->validate([
            'category' => ['required', 'string', 'in:' . implode(',', $allowedCategories)],
        ]);

        if (
            ($data['category'] ?? null) !== 'lainnya'
            && !array_key_exists($data['category'], AdminDocument::CATEGORY_OPTIONS)
        ) {
            $data['custom_category'] = $data['category'];
            $data['category'] = 'lainnya';
        }

        if (($data['category'] ?? null) === 'lainnya') {
            $request->validate([
                'custom_category' => ['required', 'string', 'max:120'],
            ]);
        }

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
}
