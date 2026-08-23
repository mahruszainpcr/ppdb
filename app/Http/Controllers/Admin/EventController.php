<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->withCount('attendances')
            ->latest('event_date')
            ->latest('id')
            ->get();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.form', [
            'event' => new Event(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request, null);
        $data['slug'] = $this->uniqueSlug(Str::slug($data['name']));

        $event = Event::create($data);

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Event berhasil ditambahkan.');
    }

    public function show(Event $event)
    {
        $event->loadCount('attendances');

        $recentAttendances = EventAttendance::query()
            ->with([
                'registration.studentProfile',
                'registration.santriContinuation',
                'registration.user',
                'scanner',
            ])
            ->where('event_id', $event->id)
            ->latest('scanned_at')
            ->latest('id')
            ->take(50)
            ->get();

        return view('admin.events.show', compact('event', 'recentAttendances'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.form', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validatedData($request, $event->id);
        $data['slug'] = $this->uniqueSlug(Str::slug($data['name']), $event->id);

        $event->update($data);

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    public function exportAttendances(Event $event): StreamedResponse
    {
        $attendances = EventAttendance::query()
            ->with(['registration.studentProfile', 'registration.santriContinuation', 'registration.user', 'scanner'])
            ->where('event_id', $event->id)
            ->latest('scanned_at')
            ->latest('id')
            ->get();

        $filename = 'histori-kehadiran-' . Str::slug($event->name) . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($attendances) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'No. Pendaftaran',
                'Nama Santri',
                'Nama Orang Tua',
                'Waktu Scan',
                'Petugas',
                'Payload Scan',
            ]);

            foreach ($attendances as $attendance) {
                fputcsv($handle, [
                    $attendance->registration?->registration_no ?? '-',
                    $attendance->registration?->studentProfile?->full_name
                        ?? $attendance->registration?->santriContinuation?->full_name
                        ?? '-',
                    $attendance->registration?->user?->name ?? '-',
                    optional($attendance->scanned_at)->format('d-m-Y H:i:s') ?? '-',
                    $attendance->scanner?->name ?? '-',
                    $attendance->scan_payload ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function destroyAttendance(Event $event, EventAttendance $attendance)
    {
        if ($attendance->event_id !== $event->id) {
            abort(404);
        }

        $attendance->delete();

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Histori absensi berhasil dihapus.');
    }

    public function scanAttendance(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'payload' => ['required', 'string', 'max:1000'],
        ]);

        $registration = $this->resolveRegistrationFromPayload($validated['payload']);

        if (!$registration) {
            return response()->json([
                'ok' => false,
                'status' => 'invalid',
                'message' => 'QR/barcode atau nomor HP orang tua tidak dikenali sebagai data pendaftaran.',
            ], 422);
        }

        $existing = EventAttendance::query()
            ->with(['scanner'])
            ->where('event_id', $event->id)
            ->where('registration_id', $registration->id)
            ->first();

        if ($existing) {
            return response()->json([
                'ok' => false,
                'status' => 'already_scanned',
                'message' => 'Pendaftar ini sudah melakukan absensi pada event ini.',
                'attendance' => [
                    'registration_no' => $registration->registration_no,
                    'student_name' => $registration->studentProfile?->full_name ?? $registration->santriContinuation?->full_name ?? '-',
                    'scanned_at' => optional($existing->scanned_at)->format('d M Y H:i:s'),
                    'scanned_by' => $existing->scanner?->name ?? '-',
                ],
            ], 409);
        }

        $attendance = DB::transaction(function () use ($event, $registration, $request, $validated) {
            return EventAttendance::create([
                'event_id' => $event->id,
                'registration_id' => $registration->id,
                'scanned_by' => $request->user()->id,
                'scanned_at' => now(),
                'scan_payload' => $validated['payload'],
            ]);
        });

        return response()->json([
            'ok' => true,
            'status' => 'success',
            'message' => 'Absensi berhasil dicatat.',
            'attendance' => [
                'id' => $attendance->id,
                'registration_no' => $registration->registration_no,
                'student_name' => $registration->studentProfile?->full_name ?? $registration->santriContinuation?->full_name ?? '-',
                'parent_name' => $registration->user?->name ?? '-',
                'scanned_at' => optional($attendance->scanned_at)->format('d M Y H:i:s'),
                'scanned_by' => $request->user()->name ?? '-',
            ],
        ]);
    }

    private function validatedData(Request $request, ?int $eventId): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:3000'],
            'event_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base ?: Str::random(8);
        $counter = 1;

        while (Event::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function resolveRegistrationFromPayload(string $payload): ?Registration
    {
        $payload = trim($payload);

        if ($payload === '') {
            return null;
        }

        $registrationNo = $this->extractRegistrationNo($payload);
        if ($registrationNo) {
            return Registration::query()
                ->with(['studentProfile', 'santriContinuation', 'user'])
                ->where('registration_no', $registrationNo)
                ->first();
        }

        $phone = $this->normalizePhone($payload);
        if ($phone === '') {
            return null;
        }

        return Registration::query()
            ->with(['studentProfile', 'santriContinuation', 'user', 'parentProfile'])
            ->whereHas('user', fn($query) => $query->where('phone', $phone))
            ->orWhereHas('parentProfile', function ($query) use ($phone) {
                $query->where('father_phone', $phone)
                    ->orWhere('mother_phone', $phone);
            })
            ->first();
    }

    private function normalizePhone(string $value): string
    {
        $clean = preg_replace('/[^0-9+]/', '', $value) ?? '';

        if ($clean === '') {
            return '';
        }

        if (str_starts_with($clean, '+62')) {
            return '0' . substr($clean, 3);
        }

        if (str_starts_with($clean, '62')) {
            return '0' . substr($clean, 2);
        }

        return $clean;
    }

    private function extractRegistrationNo(string $payload): ?string
    {
        $payload = trim($payload);

        if (preg_match('/(DS-\d{4}-[A-Z0-9]+)/i', $payload, $matches)) {
            return strtoupper($matches[1]);
        }

        $path = parse_url($payload, PHP_URL_PATH) ?? '';
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));
        $lastSegment = end($segments) ?: null;

        if ($lastSegment && preg_match('/^DS-\d{4}-[A-Z0-9]+$/i', $lastSegment)) {
            return strtoupper($lastSegment);
        }

        return null;
    }
}
