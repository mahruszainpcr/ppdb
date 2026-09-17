@php
    $exam = $registration->oralExam;
    $restoreInput = (string) old('exam_registration_id') === (string) $registration->id;
    $flagged = !empty($registration->interview?->disqualification_reasons);
    $decision = $registration->admission_decision;
    if (!$decision && $registration->graduation_status === 'tidak_lulus') { $decision = 'rejected'; }
@endphp
<article class="exam-participant {{ $flagged ? 'exam-flagged table-danger' : '' }}">
    <header class="exam-participant-header">
        <div>
            <div class="small text-muted mb-1">{{ $registration->registration_no }}</div>
            <a class="exam-student-name" href="{{ route('admin.registrations.show', $registration) }}">{{ $registration->studentProfile?->full_name ?? $registration->santriContinuation?->full_name ?? 'Nama belum diisi' }}</a>
            <div class="small text-muted mt-1">{{ $registration->period?->name ?? 'Periode belum ditentukan' }}</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-light text-dark">{{ $registration->education_level_label }}</span>
            <span class="badge bg-light text-dark">{{ match ($registration->gender) { 'male' => 'Ikhwan', 'female' => 'Akhwat', default => 'Gender belum diisi' } }}</span>
            <span class="badge bg-light text-dark">{{ match ($registration->studentProfile?->program_choice) { 'takhosus' => 'Takhosus', 'mahad' => "Ma'had", default => 'Program belum dipilih' } }}</span>
            @if ($registration->interview?->has_relative)<span class="badge bg-info text-dark">Ada saudara</span>@endif
            @if ($flagged)<span class="badge bg-danger">Tidak direkomendasikan</span>@endif
        </div>
    </header>
    <div class="exam-participant-body">
        <section class="exam-panel">
            <h6>Tahsin Lisan</h6>
            <form method="POST" action="{{ route('admin.oral-exams.save', $registration) }}">
                @csrf
                <input type="hidden" name="exam_registration_id" value="{{ $registration->id }}">
                <div class="exam-grades">
                    @foreach ([1, 2, 3] as $number)
                        @php $field = 'question_' . $number . '_grade'; $value = $restoreInput ? old($field) : $exam?->{$field}; @endphp
                        <div>
                            <label class="form-label small" for="grade-{{ $registration->id }}-{{ $number }}">Soal {{ $number }}</label>
                            <select id="grade-{{ $registration->id }}-{{ $number }}" name="{{ $field }}" class="form-select">
                                <option value="">Belum</option>
                                @foreach (['A', 'B', 'C'] as $grade)<option value="{{ $grade }}" @selected($value === $grade)>{{ $grade }}</option>@endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
                <label class="form-label small mt-3" for="notes-{{ $registration->id }}">Catatan tahsin</label>
                <textarea id="notes-{{ $registration->id }}" name="notes" class="form-control" rows="2" maxlength="2000" placeholder="Catatan bacaan santri">{{ $restoreInput ? old('notes') : $exam?->notes }}</textarea>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-3">
                    <button class="btn btn-primary btn-sm">Simpan Tahsin</button>
                    <small class="text-muted">{{ $exam?->examiner?->name ?? 'Belum dinilai' }}<br>{{ $exam?->updated_at?->format('d/m/Y H:i') }}</small>
                </div>
            </form>
        </section>
        <section class="exam-panel exam-interview">
            <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                <h6 class="mb-0">Wawancara</h6>
                <a class="small" href="{{ route('admin.interviews.edit', $registration) }}">Buka wawancara &rarr;</a>
            </div>
            @include('admin.oral-exams.interview-summary', ['interview' => $registration->interview])
        </section>
        <section class="exam-panel exam-decision">
            <h6>Keputusan Kelulusan</h6>
            <fieldset class="decision-options" data-url="{{ route('admin.oral-exams.decision', $registration) }}" data-saved="{{ $decision ?? '' }}">
                <legend class="visually-hidden">Kelulusan {{ $registration->registration_no }}</legend>
                @foreach (['pending' => 'Belum ditentukan', 'regular' => 'Lulus Reguler', 'rejected' => 'Tidak Lulus', 'takhosus' => 'Lulus Takhosus', 'scholarship' => 'Lulus Beasiswa'] as $value => $label)
                    <label class="exam-decision-choice" for="decision-{{ $registration->id }}-{{ $value }}">
                        <input type="radio" class="form-check-input" id="decision-{{ $registration->id }}-{{ $value }}" name="decision-{{ $registration->id }}" value="{{ $value }}" @checked($decision === $value)>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
                @if (!$decision)<div class="small text-muted mt-2">Status sebelumnya: {{ $registration->graduation_status }}.</div>@endif
                <div class="decision-message small mt-2" role="status" aria-live="polite"></div>
            </fieldset>
        </section>
    </div>
</article>
