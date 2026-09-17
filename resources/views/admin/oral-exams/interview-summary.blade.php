@if (!$interview)
    <span class="text-muted">Belum ada wawancara</span>
@else
    @php $rejected = !empty($interview->disqualification_reasons); @endphp
    <div class="{{ $rejected ? 'alert alert-danger' : 'border rounded p-2' }} mb-2">
        <strong>{{ $interview->recommendation_label }}</strong>
        @if ($rejected)
            <ul class="small mb-0 mt-1">@foreach ($interview->disqualification_reasons as $reason)<li>{{ $reason }}</li>@endforeach</ul>
        @else
            <div>Nilai dasar: {{ $interview->base_score ?? '-' }} + bonus {{ $interview->bonus_points }}</div>
            <div class="fw-bold">Nilai akhir: {{ $interview->total_score ?? 'Belum lengkap' }}</div>
        @endif
    </div>
    <details>
        <summary>Detail skor wawancara</summary>
        @foreach (config('ppdb_interview.sections') as $section => $group)
            @php
                $points = 0; $answered = 0;
                foreach ($group['questions'] as $key => $question) {
                    $grade = $interview->answers[$section][$key] ?? null;
                    if ($grade) { $points += config('ppdb_interview.weights')[$grade] ?? 0; $answered++; }
                }
            @endphp
            <div class="fw-semibold mt-2">{{ $group['label'] }}: {{ $points }}/{{ count($group['questions']) * 3 }} poin ({{ $answered }}/{{ count($group['questions']) }} terisi)</div>
            <ul class="small ps-3">
                @foreach ($group['questions'] as $key => $question)
                    @php $grade = $interview->answers[$section][$key] ?? null; @endphp
                    <li class="{{ $grade === 'C' && ($question['reject_c'] ?? false) ? 'text-danger fw-bold' : '' }}">{{ $question['label'] }}: {{ $grade ?? '-' }}{{ $grade ? ' (' . (config('ppdb_interview.weights')[$grade] ?? 0) . ')' : '' }}</li>
                @endforeach
            </ul>
        @endforeach
        <div class="small">Catatan: {{ $interview->notes ?: '-' }}</div>
    </details>
@endif
