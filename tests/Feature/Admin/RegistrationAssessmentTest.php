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
                    'arabic_score' => 78,
                    'tpa_score' => 85,
                    'interview_recommendation' => 'sangat_direkomendasikan',
                    'oral_question_1' => 90,
                    'oral_question_2' => 85,
                    'oral_question_3' => 80,
                    'tahsin_status' => 'diterima',
                ],
                $second->id => [
                    'arabic_score' => 75,
                    'tpa_score' => 77,
                    'interview_recommendation' => 'direkomendasikan',
                    'oral_question_1' => 70,
                    'oral_question_2' => 60,
                    'oral_question_3' => 65,
                    'tahsin_status' => 'tidak_diterima',
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('registrations', [
            'id' => $first->id,
            'tahfidz_score' => 85.00,
            'tajwid_score' => 85.00,
            'interview_recommendation' => 'sangat_direkomendasikan',
            'tahsin_status' => 'diterima',
            'oral_question_1' => 90,
            'oral_question_2' => 85,
            'oral_question_3' => 80,
        ]);
        $this->assertDatabaseHas('registrations', [
            'id' => $second->id,
            'arabic_score' => 75,
            'tpa_score' => 77,
            'interview_recommendation' => 'direkomendasikan',
            'tahsin_status' => 'tidak_diterima',
            'oral_question_3' => 65,
            'tahfidz_score' => 65.00,
            'tajwid_score' => 65.00,
        ]);
    }
}
