<?php

namespace Tests\Feature\Admin;

use App\Models\Period;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AdmissionQuotaTest extends TestCase
{
    use RefreshDatabase;

    private function registration(Period $period, string $gender = 'male', string $funding = 'mandiri'): Registration
    {
        return Registration::create([
            'user_id' => User::factory()->create()->id,
            'period_id' => $period->id,
            'registration_no' => 'TEST-' . uniqid(),
            'gender' => $gender,
            'funding_type' => $funding,
            'education_level' => 'SMP_NEW',
            'graduation_status' => 'pending',
        ]);
    }

    private function profile(Registration $registration, string $program = 'takhosus'): void
    {
        $registration->studentProfile()->create([
            'full_name' => 'Santri Test', 'birth_place' => 'Medan', 'birth_date' => '2012-01-01',
            'address' => 'Alamat', 'province' => 'Sumut', 'city' => 'Medan', 'district' => 'Medan',
            'school_origin' => 'SD', 'hobby' => 'Baca', 'ambition' => 'Guru', 'medical_history' => 'Tidak',
            'motivation' => 'self', 'quran_memorization_level' => 'ge_five',
            'quran_reading_level' => 'fluent', 'program_choice' => $program,
        ]);
    }

    public function test_admin_can_save_settings_and_invalid_limits_are_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $data = ['name' => 'PPDB Test', 'wave' => 1, 'scholarship_quota' => 10,
            'takhosus_ikhwan_quota' => 6, 'takhosus_akhwat_quota' => 3,
            'wa_group_takhosus_ikhwan' => 'https://chat.whatsapp.com/ikhwan',
            'wa_group_takhosus_akhwat' => 'https://chat.whatsapp.com/akhwat'];
        $this->post(route('admin.periods.save'), $data)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('periods', $data);
        $this->get(route('admin.periods.index'))->assertOk()->assertSee('Ketentuan Beasiswa dan Takhosus');
        $this->post(route('admin.periods.save'), array_replace($data, ['scholarship_quota' => -1]))
            ->assertSessionHasErrors('scholarship_quota');
    }

    public function test_scholarship_quota_is_combined_and_a_released_place_can_be_reused(): void
    {
        $period = Period::create(['name' => 'Test', 'wave' => 1, 'scholarship_quota' => 1]);
        $first = $this->registration($period, 'male', 'beasiswa');
        $second = $this->registration($period, 'female', 'beasiswa');
        $first->update(['graduation_status' => 'lulus']);
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = ['graduation_status' => 'lulus', 'interview_recommendation' => 'direkomendasikan'];
        $this->actingAs($admin)->post(route('admin.registrations.graduation', $second), $payload)
            ->assertSessionHasErrors('graduation_status');
        $this->assertSame('pending', $second->fresh()->graduation_status);
        $first->update(['graduation_status' => 'cadangan']);
        $this->post(route('admin.registrations.graduation', $second), $payload)->assertSessionHasNoErrors();
        $this->assertSame('lulus', $second->fresh()->graduation_status);
    }

    public function test_takhosus_quota_is_separate_by_gender_and_period(): void
    {
        $period = Period::create(['name' => 'Test', 'wave' => 1, 'takhosus_ikhwan_quota' => 1, 'takhosus_akhwat_quota' => 1]);
        foreach (['male', 'female'] as $gender) {
            $registration = $this->registration($period, $gender);
            $this->profile($registration);
            $registration->update(['graduation_status' => 'lulus']);
        }
        $other = Period::create(['name' => 'Other', 'wave' => 2]);
        $registration = $this->registration($other);
        $this->profile($registration);
        $registration->update(['graduation_status' => 'lulus']);

        $extra = $this->registration($period);
        $this->profile($extra);
        $this->expectException(ValidationException::class);
        $extra->update(['graduation_status' => 'lulus']);
    }

    public function test_changing_accepted_student_to_takhosus_cannot_bypass_zero_quota(): void
    {
        $period = Period::create(['name' => 'Test', 'wave' => 1, 'takhosus_ikhwan_quota' => 0]);
        $registration = $this->registration($period);
        $this->profile($registration, 'mahad');
        $registration->update(['graduation_status' => 'lulus']);
        $this->expectException(ValidationException::class);
        $registration->studentProfile->update(['program_choice' => 'takhosus']);
    }

    public function test_takhosus_uses_its_own_group_without_falling_back_to_regular_group(): void
    {
        $period = Period::create(['name' => 'Test', 'wave' => 1,
            'wa_group_ikhwan' => 'https://chat.whatsapp.com/regular',
            'wa_group_takhosus_ikhwan' => 'https://chat.whatsapp.com/takhosus',
            'wa_group_takhosus_akhwat' => 'https://chat.whatsapp.com/takhosus-akhwat']);
        $registration = $this->registration($period);
        $this->profile($registration);
        $this->assertSame($period->wa_group_takhosus_ikhwan, $registration->whatsappGroupLink());
        $registration->update(['gender' => 'female']);
        $this->assertSame($period->wa_group_takhosus_akhwat, $registration->whatsappGroupLink());
        $registration->update(['gender' => 'male']);
        $period->update(['wa_group_takhosus_ikhwan' => null]);
        $this->assertNull($registration->fresh()->whatsappGroupLink());
        $registration->studentProfile->update(['program_choice' => 'mahad']);
        $this->assertSame($period->wa_group_ikhwan, $registration->fresh()->whatsappGroupLink());
    }

    public function test_settings_cannot_reduce_quota_below_accepted_students(): void
    {
        $period = Period::create(['name' => 'Test', 'wave' => 1]);
        $this->registration($period, 'male', 'beasiswa')->update(['graduation_status' => 'lulus']);
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post(route('admin.periods.save'), ['period_id' => $period->id, 'name' => 'Test', 'wave' => 1,
                'scholarship_quota' => 0, 'takhosus_ikhwan_quota' => 6, 'takhosus_akhwat_quota' => 3])
            ->assertSessionHasErrors('scholarship_quota');
        $this->assertSame(10, $period->fresh()->scholarship_quota);
    }
}
