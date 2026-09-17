<?php

namespace Tests\Feature\Admin;

use App\Models\Registration;
use App\Models\User;
use App\Services\InterviewScoring;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PpdbInterviewTest extends TestCase
{
    use RefreshDatabase;

    private function answers(string $grade = 'A'): array
    {
        $answers = [];
        foreach (config('ppdb_interview.sections') as $section => $group) {
            foreach ($group['questions'] as $key => $question) {
                $answers[$section][$key] = array_key_exists($grade, $question['options']) ? $grade : 'A';
            }
        }

        return $answers;
    }

    private function registration(): Registration
    {
        return Registration::create(['user_id' => User::factory()->create(['role' => 'parent'])->id,
            'registration_no' => 'TEST-' . uniqid(), 'education_level' => 'SMP_NEW', 'gender' => 'male']);
    }

    public function test_scoring_normalizes_three_two_one_and_applies_bonus(): void
    {
        $result = InterviewScoring::calculate($this->answers(), true);
        $this->assertEquals(100, $result['base_score']);
        $this->assertEquals(105, $result['total_score']);
        $this->assertSame('sangat_direkomendasikan', $result['recommendation']);
        $answers = $this->answers('B'); // 28 B and 2 A (questions without B).
        $result = InterviewScoring::calculate($answers, false);
        $this->assertEquals(68.89, $result['total_score']);
        $this->assertSame('direkomendasikan', $result['recommendation']);
        $this->assertSame('sangat_direkomendasikan', InterviewScoring::calculate($answers, true)['recommendation']);
        $answers['child']['sport'] = 'A'; // 63 out of 90 points = exactly 70.
        $atThreshold = InterviewScoring::calculate($answers, false);
        $this->assertEquals(70, $atThreshold['total_score']);
        $this->assertSame('direkomendasikan', $atThreshold['recommendation']);
    }

    public function test_each_disqualification_overrides_scores_and_bonus(): void
    {
        foreach (['child.favorite', 'father.favorite', 'mother.favorite', 'father.attendance', 'mother.attendance'] as $field) {
            $answers = $this->answers();
            data_set($answers, $field, 'C');
            $result = InterviewScoring::calculate($answers, true);
            $this->assertSame('tidak_direkomendasikan', $result['recommendation']);
            $this->assertCount(1, $result['disqualification_reasons']);
        }
        $answers = $this->answers();
        $answers['child']['sport'] = null;
        $result = InterviewScoring::calculate($answers, true);
        $this->assertNull($result['total_score']);
        $this->assertSame('pending', $result['recommendation']);
    }

    public function test_admin_and_ustadz_can_save_with_history_and_relative_flag(): void
    {
        foreach (['admin', 'ustadz'] as $role) {
            $examiner = User::factory()->create(['role' => $role]);
            $registration = $this->registration();
            $this->actingAs($examiner)->get(route('admin.interviews.edit', $registration))->assertOk()->assertSee('Wawancara Anak');
            $data = ['answers' => $this->answers(), 'distant_city_bonus' => 1, 'relative_details' => 'Kakak: Ahmad', 'notes' => 'Catatan'];
            $url = route('admin.interviews.save', $registration);
            $this->post($url, $data)->assertSessionHasNoErrors();
            $interview = $registration->interview()->sole();
            $this->assertTrue($interview->has_relative);
            $this->assertEquals(105, $interview->total_score);
            $this->assertSame($examiner->id, $interview->examiner_id);
            $this->assertSame('interview_created', $registration->audits()->sole()->action);
            $this->get(route('admin.registrations.show', $registration))->assertOk()->assertSee('Ada saudara di Darussalam');
            $this->post($url, $data)->assertSessionHasNoErrors();
            $this->assertSame(1, $registration->audits()->count());
            $data['answers']['child']['relative'] = 'C';
            $data['answers']['mother']['favorite'] = 'C';
            $this->post($url, $data)->assertSessionHasNoErrors();
            $interview->refresh();
            $this->assertFalse($interview->has_relative);
            $this->assertNull($interview->relative_details);
            $this->assertSame('tidak_direkomendasikan', $interview->recommendation);
            $audit = $registration->audits()->latest('id')->first();
            $this->assertSame('interview_updated', $audit->action);
            $this->assertSame($examiner->id, $audit->user_id);
            $this->assertContains(['old' => 'A', 'new' => 'C'], array_values($audit->changes));
            $this->get(route('admin.registrations.show', $registration))->assertOk()->assertSee('Input Wawancara')->assertSee('Memperbarui wawancara PPDB');
        }
    }

    public function test_invalid_answers_and_forged_score_are_not_saved(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $registration = $this->registration();
        $answers = $this->answers();
        $answers['child']['hitting'] = 'B';
        $this->post(route('admin.interviews.save', $registration), ['answers' => $answers, 'distant_city_bonus' => 0])
            ->assertSessionHasErrors('answers.child.hitting');
        $this->assertDatabaseCount('ppdb_interviews', 0);
        $this->post(route('admin.interviews.save', $registration), ['answers' => $this->answers(),
            'distant_city_bonus' => 0, 'total_score' => 999, 'recommendation' => 'tidak_direkomendasikan'])
            ->assertSessionHasNoErrors();
        $this->assertEquals(100, $registration->interview->total_score);
    }

    public function test_parent_cannot_read_or_write_interviews(): void
    {
        $registration = $this->registration();
        $this->actingAs($registration->user)->get(route('admin.interviews.edit', $registration))->assertForbidden();
        $this->post(route('admin.interviews.save', $registration), ['answers' => $this->answers(), 'distant_city_bonus' => 1])->assertForbidden();
    }
}
