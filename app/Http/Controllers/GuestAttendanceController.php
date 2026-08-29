<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestAttendanceController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('event_date')->orWhereDate('event_date', '>=', today());
            })
            ->orderBy('event_date')
            ->orderBy('name')
            ->get();

        return view('public.attendance.index', compact('events'));
    }

    public function show(Event $event)
    {
        abort_unless($event->is_active, 404);

        return view('public.attendance.show', [
            'event' => $event,
            'participants' => collect(),
            'phone' => null,
        ]);
    }

    public function lookup(Request $request, Event $event)
    {
        abort_unless($event->is_active, 404);

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
        ]);
        $phone = $this->normalizePhone($validated['phone']);

        $participants = Registration::query()
            ->with(['studentProfile', 'santriContinuation', 'eventAttendances' => fn ($query) => $query->where('event_id', $event->id)])
            ->where(function ($query) use ($phone) {
                $query->whereHas('user', fn ($user) => $user->where('phone', $phone))
                    ->orWhereHas('parentProfile', function ($parent) use ($phone) {
                        $parent->where('father_phone', $phone)->orWhere('mother_phone', $phone);
                    });
            })
            ->orderBy('registration_no')
            ->get();

        return view('public.attendance.show', compact('event', 'participants', 'phone'));
    }

    public function store(Request $request, Event $event)
    {
        abort_unless($event->is_active, 404);

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'registration_ids' => ['required', 'array', 'min:1'],
            'registration_ids.*' => ['integer'],
        ]);
        $phone = $this->normalizePhone($validated['phone']);

        $registrationIds = collect($validated['registration_ids'])->map(fn ($id) => (int) $id)->unique()->values();
        $registrations = Registration::query()
            ->whereIn('id', $registrationIds)
            ->where(function ($query) use ($phone) {
                $query->whereHas('user', fn ($user) => $user->where('phone', $phone))
                    ->orWhereHas('parentProfile', function ($parent) use ($phone) {
                        $parent->where('father_phone', $phone)->orWhere('mother_phone', $phone);
                    });
            })
            ->get();

        if ($registrations->count() !== $registrationIds->count()) {
            return back()->withErrors(['registration_ids' => 'Peserta yang dipilih tidak cocok dengan nomor WhatsApp wali.']);
        }

        $newAttendanceCount = DB::transaction(function () use ($event, $registrations, $validated) {
            $count = 0;

            foreach ($registrations as $registration) {
                $alreadyPresent = EventAttendance::query()
                    ->where('event_id', $event->id)
                    ->where('registration_id', $registration->id)
                    ->exists();

                if ($alreadyPresent) {
                    continue;
                }

                EventAttendance::create([
                    'event_id' => $event->id,
                    'registration_id' => $registration->id,
                    'scanned_at' => now(),
                    'scan_payload' => 'guest:' . $this->normalizePhone($validated['phone']),
                ]);
                $count++;
            }

            return $count;
        });

        $message = $newAttendanceCount > 0
            ? $newAttendanceCount . ' peserta berhasil dicatat hadir. Terima kasih.'
            : 'Semua peserta yang dipilih sudah tercatat hadir pada event ini.';

        return redirect()->route('attendance.event', $event)->with('attendance_status', $message);
    }

    private function normalizePhone(string $value): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $value) ?? '';

        if (str_starts_with($phone, '+62')) {
            return '0' . substr($phone, 3);
        }

        if (str_starts_with($phone, '62')) {
            return '0' . substr($phone, 2);
        }

        return $phone;
    }
}