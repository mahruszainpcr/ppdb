<?php

namespace Tests\Feature\Admin;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationAssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_multiple_assessments_at_once(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $first = Registration::create([
            'user_id' => $admin->id,
            'registration_no' => 'DS-2026-1001',
            'education_level' => 'SMP_NEW',
            'gender' => 'male',
        ]);
        $second = Registration::create([
            'user_id' => $admin->id,
            'registration_no' => 'DS-2026-1002',
            'education_level' => 'SMA_NEW',
            'gender' => 'female',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.registrations.assessments.save'), [
            'assessments' => [
                $first->id => [
                    'tahfidz_score' => 80,
                    'tajwid_score' => 82,
                    'arabic_score' => 78,
                    'tpa_score' => 85,
                    'interview_recommendation' => 'sangat_direkomendasikan',
                    'oral_exam_notes' => 'Bacaan baik.',
                ],
                $second->id => [
                    'tahfidz_score' => 70,
                    'tajwid_score' => 72,
                    'arabic_score' => 75,
                    'tpa_score' => 77,
                    'interview_recommendation' => 'direkomendasikan',
                    'oral_exam_notes' => 'Perlu latihan tajwid.',
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('registrations', [
            'id' => $first->id,
            'tahfidz_score' => 80,
            'tajwid_score' => 82,
            'interview_recommendation' => 'sangat_direkomendasikan',
        ]);
        $this->assertDatabaseHas('registrations', [
            'id' => $second->id,
            'arabic_score' => 75,
            'tpa_score' => 77,
            'interview_recommendation' => 'direkomendasikan',
        ]);
    }
}
