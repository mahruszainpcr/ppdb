<?php

namespace Tests\Feature\Admin;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_delete_a_registration(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);
        $registration = Registration::create([
            'user_id' => $parent->id, 'registration_no' => 'TEST-DELETE',
            'education_level' => 'SMP_NEW', 'gender' => 'male',
        ]);
        $url = route('admin.registrations.destroy', $registration);

        $this->actingAs($parent)->delete($url)->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'ustadz']))->delete($url)->assertForbidden();
        $this->assertDatabaseHas('registrations', ['id' => $registration->id]);
        $this->get(route('admin.registrations.show', $registration))->assertOk()->assertDontSee('Hapus Data');

        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get(route('admin.registrations.show', $registration))->assertOk()->assertSee('Hapus Data');
        $this->delete($url)->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseMissing('registrations', ['id' => $registration->id]);
    }
}
