<?php

namespace Tests\Feature\Admin;

use App\Models\Period;
use App\Models\Registration;
use App\Models\User;
use App\Services\AdmissionQuota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdmissionDecisionTest extends TestCase
{
    use RefreshDatabase;

    private function registration(Period $period, string $gender = 'male'): Registration
    {
        return Registration::create(['user_id' => User::factory()->create(['role' => 'parent'])->id,
            'period_id' => $period->id, 'registration_no' => uniqid('TEST-'), 'gender' => $gender, 'education_level' => 'SMP_NEW']);
    }

    public function test_ajax_decisions_obey_quota_and_release_places_and_record_history(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $period = Period::create(['name' => 'Test', 'wave' => 1, 'scholarship_quota' => 1, 'takhosus_ikhwan_quota' => 1]);
        $first = $this->registration($period);
        $second = $this->registration($period);
        $url = fn ($registration) => route('admin.oral-exams.decision', $registration);
        $this->postJson($url($first), ['decision' => 'scholarship'])->assertOk()->assertJsonPath('usage.scholarship_quota', 1);
        $this->postJson($url($second), ['decision' => 'scholarship'])->assertUnprocessable()->assertJsonValidationErrors('graduation_status');
        $this->assertNull($second->fresh()->admission_decision);
        $this->postJson($url($first), ['decision' => 'takhosus'])->assertOk()->assertJsonPath('usage.scholarship_quota', 0);
        $this->postJson($url($second), ['decision' => 'scholarship'])->assertOk();
        $this->postJson($url($second), ['decision' => 'takhosus'])->assertUnprocessable();
        $this->assertSame('scholarship', $second->fresh()->admission_decision);
        $this->postJson($url($first), ['decision' => 'rejected'])->assertOk();
        $this->assertSame('tidak_lulus', $first->fresh()->graduation_status);
        $this->postJson($url($second), ['decision' => 'takhosus'])->assertOk();
        $this->assertSame('lulus', $second->fresh()->graduation_status);
        $audit = $second->audits()->latest('id')->first();
        $this->assertSame(['old' => 'scholarship', 'new' => 'takhosus'], $audit->changes['admission_decision']);
        $this->postJson($url($second), ['decision' => 'regular'])->assertOk()->assertJsonPath('usage.takhosus_ikhwan_quota', 0);
        $this->assertSame(0, AdmissionQuota::usage($period)['scholarship_quota']);
    }

    public function test_gender_quota_and_role_access_and_validation(): void
    {
        $period = Period::create(['name' => 'Test', 'wave' => 1, 'takhosus_ikhwan_quota' => 0, 'takhosus_akhwat_quota' => 1]);
        $male = $this->registration($period);
        $female = $this->registration($period, 'female');
        $this->actingAs(User::factory()->create(['role' => 'ustadz']));
        $this->postJson(route('admin.oral-exams.decision', $male), ['decision' => 'takhosus'])->assertUnprocessable();
        $this->postJson(route('admin.oral-exams.decision', $female), ['decision' => 'takhosus'])->assertOk();
        $this->postJson(route('admin.oral-exams.decision', $female), ['decision' => 'invalid'])->assertUnprocessable();
        $this->actingAs($male->user)->postJson(route('admin.oral-exams.decision', $male), ['decision' => 'regular'])->assertForbidden();
    }

    public function test_exam_page_shows_red_reasons_and_interview_details(): void
    {
        $period = Period::create(['name' => 'Test', 'wave' => 1]);
        $registration = $this->registration($period);
        $registration->interview()->create(['answers' => ['child' => ['favorite' => 'C']],
            'recommendation' => 'tidak_direkomendasikan', 'disqualification_reasons' => ['Ustadz favorit: jawaban C']]);
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.oral-exams.index'))->assertOk()
            ->assertSee('Ujian PPDB')->assertSee('Lulus Reguler')->assertSee('Lulus Beasiswa')
            ->assertSee('table-danger')->assertSee('Ustadz favorit: jawaban C')->assertSee('Detail skor wawancara');
    }
}
