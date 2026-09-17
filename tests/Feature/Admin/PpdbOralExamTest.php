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
                'question_1_grade' => 'A', 'question_2_grade' => 'B', 'question_3_grade' => 'C', 'notes' => 'Perbaiki makhraj.',
            ])->assertRedirect($detail)->assertSessionHasNoErrors();
            $this->assertDatabaseHas('ppdb_oral_exams', [
                'registration_id' => $registration->id, 'question_1_grade' => 'A',
                'question_2_grade' => 'B', 'question_3_grade' => 'C', 'notes' => 'Perbaiki makhraj.', 'examiner_id' => $user->id,
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
        $data = ['question_1_grade' => 'A', 'question_2_grade' => 'B', 'question_3_grade' => 'C', 'notes' => 'Catatan'];
        foreach ([$first, $second] as $registration) {
            $this->post(route('admin.oral-exams.save', $registration), $data)->assertSessionHasNoErrors();
        }
        $this->post(route('admin.oral-exams.save', $first), array_replace($data, [
            'question_1_grade' => 'C', 'question_2_grade' => '', 'notes' => '',
        ]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('ppdb_oral_exams', 2);
        $this->assertSame('A', $second->oralExam->question_1_grade);
        $this->assertSame('C', $first->oralExam->question_1_grade);
        $this->assertNull($first->oralExam->question_2_grade);
        $this->assertNull($first->oralExam->notes);
        $this->assertSame(0, $first->fresh()->oral_question_1);
    }

    public function test_invalid_grades_and_long_notes_are_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->post(route('admin.oral-exams.save', $this->registration()), [
            'question_1_grade' => 'D', 'question_2_grade' => 90, 'question_3_grade' => 'A', 'notes' => str_repeat('x', 2001),
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
            'question_1_grade' => 'A', 'question_2_grade' => 'A', 'question_3_grade' => 'A', 'notes' => null,
        ])->assertForbidden();
        $this->assertDatabaseCount('ppdb_oral_exams', 0);
    }

    public function test_history_records_actor_and_changes_without_duplicate_entries(): void
    {
        $registration = $this->registration();
        $admin = User::factory()->create(['role' => 'admin']);
        $ustadz = User::factory()->create(['role' => 'ustadz']);
        $url = route('admin.oral-exams.save', $registration);
        $data = ['question_1_grade' => 'A', 'question_2_grade' => 'B', 'question_3_grade' => 'C', 'notes' => 'Catatan awal'];
        $this->actingAs($admin)->post($url, $data)->assertSessionHasNoErrors();
        $created = $registration->audits()->sole();
        $this->assertSame('oral_exam_created', $created->action);
        $this->assertSame($admin->id, $created->user_id);
        $this->assertSame(['old' => null, 'new' => 'A'], $created->changes['question_1_grade']);

        $this->actingAs($ustadz)->post($url, $data)->assertSessionHasNoErrors();
        $this->assertSame(1, $registration->audits()->count());
        $this->assertSame($admin->id, $registration->oralExam()->first()->examiner_id);

        $this->post($url, array_replace($data, ['question_1_grade' => 'C', 'notes' => '']))->assertSessionHasNoErrors();
        $updated = $registration->audits()->latest('id')->first();
        $this->assertSame('oral_exam_updated', $updated->action);
        $this->assertSame($ustadz->id, $updated->user_id);
        $this->assertSame([
            'question_1_grade' => ['old' => 'A', 'new' => 'C'],
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
