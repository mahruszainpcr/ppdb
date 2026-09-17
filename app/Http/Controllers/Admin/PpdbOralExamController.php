<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Period;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PpdbOralExamController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'period_id' => ['nullable', 'integer', 'exists:periods,id'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'program' => ['nullable', Rule::in(['mahad', 'takhosus'])],
        ]);
        $query = Registration::query()->with(['studentProfile', 'santriContinuation', 'period', 'oralExam.examiner', 'interview']);
        if ($search = trim($filters['search'] ?? '')) {
            $query->where(function ($q) use ($search) {
                $q->where('registration_no', 'like', "%{$search}%")
                    ->orWhereHas('studentProfile', fn ($student) => $student->where('full_name', 'like', "%{$search}%"))
                    ->orWhereHas('santriContinuation', fn ($student) => $student->where('full_name', 'like', "%{$search}%"));
            });
        }
        foreach (['period_id', 'gender'] as $field) {
            if (!empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if (!empty($filters['program'])) {
            $query->whereHas('studentProfile', fn ($q) => $q->where('program_choice', $filters['program']));
        }

        $registrations = $query->latest('id')->paginate(25)->withQueryString();
        $quotaPeriods = $registrations->getCollection()->pluck('period')->filter()->unique('id');
        return view('admin.oral-exams.index', [
            'registrations' => $registrations,
            'quotaPeriods' => $quotaPeriods,
            'quotaUsage' => $quotaPeriods->mapWithKeys(fn ($period) => [$period->id => \App\Services\AdmissionQuota::usage($period)]),
            'periods' => Period::query()->latest('id')->get(),
        ]);
    }

    public function save(Request $request, Registration $registration)
    {
        $data = $request->validate([
            'question_1_grade' => ['present', 'nullable', Rule::in(['A', 'B', 'C'])],
            'question_2_grade' => ['present', 'nullable', Rule::in(['A', 'B', 'C'])],
            'question_3_grade' => ['present', 'nullable', Rule::in(['A', 'B', 'C'])],
            'notes' => ['present', 'nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($registration, $request, $data) {
            Registration::query()->lockForUpdate()->findOrFail($registration->id);
            $exam = $registration->oralExam()->firstOrNew();
            $isNew = !$exam->exists;
            $changes = [];
            foreach ($data as $field => $value) {
                $previous = $exam->{$field};
                if ($isNew || $previous !== $value) {
                    $changes[$field] = ['old' => $previous, 'new' => $value];
                }
            }

            if ($changes === []) {
                return;
            }

            $exam->fill($data + ['examiner_id' => $request->user()->id])->save();
            $registration->audits()->create([
                'user_id' => $request->user()->id,
                'action' => $isNew ? 'oral_exam_created' : 'oral_exam_updated',
                'changes' => $changes,
            ]);
        });

        return back()->with('success', 'Nilai tahsin lisan ' . $registration->registration_no . ' berhasil disimpan.');
    }

    public function decision(Request $request, Registration $registration)
    {
        $data = $request->validate(['decision' => ['required', Rule::in(['pending', 'regular', 'rejected', 'takhosus', 'scholarship'])]]);
        return DB::transaction(function () use ($registration, $data) {
            $period = $registration->period_id ? Period::query()->lockForUpdate()->findOrFail($registration->period_id) : null;
            $registration = Registration::query()->lockForUpdate()->findOrFail($registration->id);
            if (in_array($data['decision'], ['takhosus', 'scholarship'], true) && !$period) {
                throw \Illuminate\Validation\ValidationException::withMessages(['decision' => 'Tetapkan periode santri sebelum menentukan kelulusan dengan kuota.']);
            }
            $registration->update([
                'admission_decision' => $data['decision'],
                'graduation_status' => match ($data['decision']) {
                    'pending' => 'pending', 'rejected' => 'tidak_lulus', default => 'lulus',
                },
            ]);

            return response()->json([
                'message' => 'Keputusan kelulusan berhasil disimpan.',
                'decision' => $registration->admission_decision,
                'period_id' => $period?->id,
                'usage' => $period ? \App\Services\AdmissionQuota::usage($period) : [],
            ]);
        });
    }
}
