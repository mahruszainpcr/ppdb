<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\InterviewScoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PpdbInterviewController extends Controller
{
    public function edit(Registration $registration)
    {
        $registration->load(['studentProfile', 'santriContinuation', 'interview.examiner']);

        return view('admin.interviews.edit', [
            'registration' => $registration,
            'interview' => $registration->interview,
            'sections' => config('ppdb_interview.sections'),
        ]);
    }

    public function save(Request $request, Registration $registration)
    {
        $sections = config('ppdb_interview.sections');
        $rules = [
            'answers' => ['required', 'array:' . implode(',', array_keys($sections))],
            'distant_city_bonus' => ['required', 'boolean'],
            'relative_details' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:4000'],
        ];
        foreach ($sections as $section => $group) {
            $rules["answers.$section"] = ['required', 'array:' . implode(',', array_keys($group['questions']))];
            foreach ($group['questions'] as $key => $question) {
                $rules["answers.$section.$key"] = ['present', 'nullable', Rule::in(array_keys($question['options']))];
            }
        }
        $data = $request->validate($rules);
        $data['distant_city_bonus'] = $request->boolean('distant_city_bonus');
        $data['notes'] = $data['notes'] ?? null;
        $data['relative_details'] = in_array($data['answers']['child']['relative'] ?? null, ['A', 'B'], true)
            ? ($data['relative_details'] ?? null) : null;

        DB::transaction(function () use ($request, $registration, $data, $sections) {
            Registration::query()->lockForUpdate()->findOrFail($registration->id);
            $interview = $registration->interview()->firstOrNew();
            $isNew = !$interview->exists;
            $changes = [];
            foreach ($sections as $section => $group) {
                foreach ($group['questions'] as $key => $question) {
                    $old = $interview->answers[$section][$key] ?? null;
                    $new = $data['answers'][$section][$key];
                    if ($old !== $new) {
                        $changes['Wawancara ' . $group['label'] . ': ' . $question['label']] = ['old' => $old, 'new' => $new];
                    }
                }
            }
            foreach (['distant_city_bonus' => 'Bonus luar kota', 'relative_details' => 'Keterangan saudara', 'notes' => 'Catatan wawancara'] as $field => $label) {
                $old = $interview->{$field};
                $new = $data[$field];
                if ($old !== $new) {
                    $changes[$label] = ['old' => is_bool($old) ? ($old ? 'Ya' : 'Tidak') : $old,
                        'new' => is_bool($new) ? ($new ? 'Ya' : 'Tidak') : $new];
                }
            }
            if (!$isNew && $changes === []) {
                return;
            }
            $result = InterviewScoring::calculate($data['answers'], $data['distant_city_bonus']);
            foreach (['total_score' => 'Nilai akhir wawancara', 'recommendation' => 'Rekomendasi wawancara'] as $field => $label) {
                if ((string) $interview->{$field} !== (string) $result[$field]) {
                    $changes[$label] = ['old' => $interview->{$field}, 'new' => $result[$field]];
                }
            }
            $interview->fill($data + $result + ['examiner_id' => $request->user()->id])->save();
            $registration->audits()->create([
                'user_id' => $request->user()->id,
                'action' => $isNew ? 'interview_created' : 'interview_updated',
                'changes' => $changes,
            ]);
        });

        return redirect()->route('admin.interviews.edit', $registration)->with('success', 'Wawancara berhasil disimpan.');
    }
}
