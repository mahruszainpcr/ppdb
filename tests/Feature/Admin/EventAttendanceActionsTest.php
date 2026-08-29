<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventAttendanceActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_event_to_inactive(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::create([
            'name' => 'Seleksi PPDB',
            'slug' => 'seleksi-ppdb',
            'event_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.events.update', $event), [
            'name' => $event->name,
            'event_date' => now()->toDateString(),
            'location' => 'Aula',
            'description' => 'Event nonaktif',
            'is_active' => false,
        ]);

        $response->assertRedirect(route('admin.events.show', $event));
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_event_attendance_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::create([
            'name' => 'Try Out',
            'slug' => 'try-out',
            'description' => 'Test event',
            'event_date' => now()->toDateString(),
            'location' => 'Lab',
            'is_active' => true,
        ]);
        $registration = Registration::create([
            'user_id' => $admin->id,
            'registration_no' => 'DS-2026-0001',
            'education_level' => 'SMA_NEW',
            'gender' => 'male',
            'status' => 'submitted',
            'funding_type' => 'mandiri',
        ]);
        $attendance = EventAttendance::create([
            'event_id' => $event->id,
            'registration_id' => $registration->id,
            'scanned_by' => $admin->id,
            'scanned_at' => now(),
            'scan_payload' => 'DS-2026-0001',
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.events.attendance.destroy', [$event, $attendance]));

        $response->assertRedirect(route('admin.events.show', $event));
        $this->assertDatabaseMissing('event_attendances', ['id' => $attendance->id]);
    }

    public function test_admin_can_export_event_attendance_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::create([
            'name' => 'Try Out',
            'slug' => 'try-out-export',
            'description' => 'Test event',
            'event_date' => now()->toDateString(),
            'location' => 'Lab',
            'is_active' => true,
        ]);
        $registration = Registration::create([
            'user_id' => $admin->id,
            'registration_no' => 'DS-2026-0002',
            'education_level' => 'SMA_NEW',
            'gender' => 'female',
            'status' => 'submitted',
            'funding_type' => 'mandiri',
        ]);
        EventAttendance::create([
            'event_id' => $event->id,
            'registration_id' => $registration->id,
            'scanned_by' => $admin->id,
            'scanned_at' => now(),
            'scan_payload' => 'DS-2026-0002',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.events.attendances.export', $event));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertHeader('content-disposition');
    }
}
