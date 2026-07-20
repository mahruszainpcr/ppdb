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

    public function scanAttendance(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'payload' => ['required', 'string', 'max:1000'],
        ]);

        $registrationNo = $this->extractRegistrationNo($validated['payload']);

        if (!$registrationNo) {
            return response()->json([
                'ok' => false,
                'status' => 'invalid',
                'message' => 'QR/barcode tidak dikenali sebagai data pendaftaran.',
            ], 422);
        }

        $registration = Registration::query()
            ->with(['studentProfile', 'santriContinuation', 'user'])
            ->where('registration_no', $registrationNo)
            ->first();

        if (!$registration) {
            return response()->json([
                'ok' => false,
                'status' => 'not_found',
                'message' => 'Data pendaftar tidak ditemukan untuk barcode tersebut.',
            ], 404);
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
