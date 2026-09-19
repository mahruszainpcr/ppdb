<?php

namespace Tests\Feature\Admin;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PpdbOralExamTest extends TestCase
{
    use RefreshDatabase;

    private function registration(): Registration
    {
        return Registration::create([
            'user_id' => User::factory()->create(['role' => 'parent'])->id,
            'registration_no' => 'TEST-' . uniqid(),
            'education_level' => 'SMP_NEW', 'gender' => 'male',
        ]);
    }

    public function test_admin_and_ustadz_can_open_detail_and_save_tahsin_grades(): void
    {
        foreach (['admin', 'ustadz'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $registration = $this->registration();
            $detail = route('admin.registrations.show', $registration);
            $this->actingAs($user)->get($detail)->assertOk()->assertSee('Input Ujian Tahsin')
                ->assertSee(route('admin.oral-exams.save', $registration), false);
            $this->from($detail)->post(route('admin.oral-exams.save', $registration), [
                'question_1_grade' => '90.00', 'question_2_grade' => '80.50', 'question_3_grade' => '70.00', 'notes' => 'Perbaiki makhraj.',
            ])->assertRedirect($detail)->assertSessionHasNoErrors();
            $this->assertDatabaseHas('ppdb_oral_exams', [
                'registration_id' => $registration->id, 'question_1_grade' => '90.00',
                'question_2_grade' => '80.50', 'question_3_grade' => '70.00', 'notes' => 'Perbaiki makhraj.', 'examiner_id' => $user->id,
            ]);
            $this->get($detail)->assertOk()->assertSee('Perbaiki makhraj.');
        }
        $this->get(route('admin.oral-exams.index'))->assertOk()->assertSee('Ujian Tahsin Lisan PPDB');
    }

    public function test_saving_again_updates_only_the_selected_students_exam(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'ustadz']));
        $first = $this->registration();
        $second = $this->registration();
        $data = ['question_1_grade' => '90.00', 'question_2_grade' => '80.50', 'question_3_grade' => '70.00', 'notes' => 'Catatan'];
        foreach ([$first, $second] as $registration) {
            $this->post(route('admin.oral-exams.save', $registration), $data)->assertSessionHasNoErrors();
        }
        $this->post(route('admin.oral-exams.save', $first), array_replace($data, [
            'question_1_grade' => '70.00', 'question_2_grade' => '', 'notes' => '',
        ]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('ppdb_oral_exams', 2);
        $this->assertSame('90.00', $second->oralExam->question_1_grade);
        $this->assertSame('70.00', $first->oralExam->question_1_grade);
        $this->assertNull($first->oralExam->question_2_grade);
        $this->assertNull($first->oralExam->notes);
        $this->assertSame(0, $first->fresh()->oral_question_1);
    }

    public function test_tpa_and_arabic_scores_are_saved_preserved_and_cleared(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'ustadz']));
        $registration = $this->registration();
        $url = route('admin.oral-exams.save', $registration);
        $data = ['question_1_grade' => '90.00', 'question_2_grade' => null, 'question_3_grade' => null, 'notes' => null];

        $this->post($url, $data + ['tpa_score' => '85.50', 'arabic_score' => '90.25'])->assertSessionHasNoErrors();
        $this->assertSame('85.50', $registration->fresh()->tpa_score);
        $this->assertSame('90.25', $registration->fresh()->arabic_score);
        $this->get(route('admin.oral-exams.index'))->assertOk()
            ->assertSee('Nilai TPA')->assertSee('Nilai Bahasa Arab')->assertSee('value="85.50"', false);
        $this->get(route('admin.registrations.show', $registration))->assertOk()
            ->assertSee('id="tahsin-arabic_score"', false)->assertSee('value="90.25"', false);

        $auditCount = $registration->audits()->count();
        $this->post($url, $data + ['tpa_score' => '85.50', 'arabic_score' => '90.25'])->assertSessionHasNoErrors();
        $this->assertSame($auditCount, $registration->audits()->count());
        $this->post($url, $data)->assertSessionHasNoErrors();
        $this->assertSame('85.50', $registration->fresh()->tpa_score);
        $this->assertSame('90.25', $registration->fresh()->arabic_score);

        $this->post($url, $data + ['tpa_score' => '0', 'arabic_score' => '100'])->assertSessionHasNoErrors();
        $this->assertSame('0.00', $registration->fresh()->tpa_score);
        $this->assertSame('100.00', $registration->fresh()->arabic_score);
        $this->post($url, $data + ['tpa_score' => '', 'arabic_score' => ''])->assertSessionHasNoErrors();
        $this->assertNull($registration->fresh()->tpa_score);
        $this->assertNull($registration->fresh()->arabic_score);
    }

    public function test_invalid_tpa_and_arabic_scores_do_not_save_exam(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $registration = $this->registration();
        foreach ([[-1, 101], [101, -1], ['invalid', 'invalid']] as [$tpa, $arabic]) {
            $this->post(route('admin.oral-exams.save', $registration), [
                'question_1_grade' => '90.00', 'question_2_grade' => null, 'question_3_grade' => null,
                'notes' => null, 'tpa_score' => $tpa, 'arabic_score' => $arabic,
            ])->assertSessionHasErrors(['tpa_score', 'arabic_score']);
        }
        $this->assertDatabaseCount('ppdb_oral_exams', 0);
        $this->assertNull($registration->fresh()->tpa_score);
        $this->assertNull($registration->fresh()->arabic_score);
    }

    public function test_numeric_scores_accept_boundaries_and_preserve_legacy_grades_until_replaced(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $registration = $this->registration();
        $registration->oralExam()->create(['question_1_grade' => 'A', 'question_2_grade' => 'B', 'question_3_grade' => 'C']);
        $url = route('admin.oral-exams.save', $registration);
        $this->get(route('admin.oral-exams.index'))->assertOk()->assertSee('Nilai lama: A');
        $this->post($url, ['question_1_grade' => '', 'question_2_grade' => '', 'question_3_grade' => '', 'notes' => null])->assertSessionHasNoErrors();
        $this->assertSame('A', $registration->oralExam()->first()->question_1_grade);

        $data = ['question_1_grade' => 0, 'question_2_grade' => 100, 'question_3_grade' => 85.25, 'notes' => null];
        $this->post($url, $data)->assertSessionHasNoErrors();
        $exam = $registration->oralExam()->first();
        $this->assertSame('0.00', $exam->question_1_grade);
        $this->assertSame('100.00', $exam->question_2_grade);
        $this->assertSame('85.25', $exam->question_3_grade);
        $auditCount = $registration->audits()->count();
        $this->post($url, $data)->assertSessionHasNoErrors();
        $this->assertSame($auditCount, $registration->audits()->count());
        foreach (['A', -1, 101] as $invalid) {
            $this->post($url, array_replace($data, ['question_1_grade' => $invalid]))->assertSessionHasErrors('question_1_grade');
        }
    }

    public function test_invalid_grades_and_long_notes_are_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->post(route('admin.oral-exams.save', $this->registration()), [
            'question_1_grade' => 'D', 'question_2_grade' => 101, 'question_3_grade' => '90.00', 'notes' => str_repeat('x', 2001),
        ])->assertSessionHasErrors(['question_1_grade', 'question_2_grade', 'notes']);
        $this->assertDatabaseCount('ppdb_oral_exams', 0);
    }

    public function test_parent_cannot_access_or_submit_admin_exam(): void
    {
        $registration = $this->registration();
        $this->actingAs($registration->user);
        $this->get(route('admin.registrations.show', $registration))->assertForbidden();
        $this->get(route('admin.oral-exams.index'))->assertForbidden();
        $this->post(route('admin.oral-exams.save', $registration), [
            'question_1_grade' => '90.00', 'question_2_grade' => '90.00', 'question_3_grade' => '90.00', 'notes' => null,
        ])->assertForbidden();
        $this->assertDatabaseCount('ppdb_oral_exams', 0);
    }

    public function test_history_records_actor_and_changes_without_duplicate_entries(): void
    {
        $registration = $this->registration();
        $admin = User::factory()->create(['role' => 'admin']);
        $ustadz = User::factory()->create(['role' => 'ustadz']);
        $url = route('admin.oral-exams.save', $registration);
        $data = ['question_1_grade' => '90.00', 'question_2_grade' => '80.50', 'question_3_grade' => '70.00', 'notes' => 'Catatan awal'];
        $this->actingAs($admin)->post($url, $data)->assertSessionHasNoErrors();
        $created = $registration->audits()->sole();
        $this->assertSame('oral_exam_created', $created->action);
        $this->assertSame($admin->id, $created->user_id);
        $this->assertSame(['old' => null, 'new' => '90.00'], $created->changes['question_1_grade']);

        $this->actingAs($ustadz)->post($url, $data)->assertSessionHasNoErrors();
        $this->assertSame(1, $registration->audits()->count());
        $this->assertSame($admin->id, $registration->oralExam()->first()->examiner_id);

        $this->post($url, array_replace($data, ['question_1_grade' => '70.00', 'notes' => '']))->assertSessionHasNoErrors();
        $updated = $registration->audits()->latest('id')->first();
        $this->assertSame('oral_exam_updated', $updated->action);
        $this->assertSame($ustadz->id, $updated->user_id);
        $this->assertSame([
            'question_1_grade' => ['old' => '90.00', 'new' => '70.00'],
            'notes' => ['old' => 'Catatan awal', 'new' => null],
        ], $updated->changes);
        $this->assertNotNull($updated->created_at);
        $this->post($url, array_replace($data, ['question_1_grade' => 'D']))->assertSessionHasErrors('question_1_grade');
        $this->assertSame(2, $registration->audits()->count());
        $this->get(route('admin.registrations.show', $registration))->assertOk()
            ->assertSee('Mengisi ujian tahsin lisan')->assertSee('Memperbarui ujian tahsin lisan')
            ->assertSee('Tahsin lisan soal 1')->assertSee('Catatan awal');
    }
}
