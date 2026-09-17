@extends('layouts.app')
@section('title', 'Ujian PPDB')

@push('styles')
<style>
    .exam-page { max-width: 1600px; margin: 0 auto; padding: 12px; }
    .exam-page .card { border: 1px solid #dfe7e2; border-radius: 12px; background: #fff; }
    .exam-page .form-control, .exam-page .form-select { min-height: 42px; padding: 9px 12px; font-size: 14px; }
    .exam-page .form-select { padding-right: 32px; }
    .exam-participant { margin-bottom: 20px; border: 1px solid #dfe7e2; border-radius: 14px; background: #fff; overflow: hidden; }
    .exam-participant-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; padding: 18px 22px; background: #f8faf9; border-bottom: 1px solid #e5ebe7; }
    .exam-student-name { font-size: 17px; font-weight: 700; color: #164d37; }
    .exam-participant-body { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr) minmax(230px, .85fr); }
    .exam-panel { padding: 22px; min-width: 0; overflow-wrap: anywhere; }
    .exam-panel + .exam-panel { border-left: 1px solid #e5ebe7; }
    .exam-panel h6 { font-size: 14px; margin-bottom: 16px; }
    .exam-grades { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
    .exam-decision { background: #fafcfb; }
    .exam-decision fieldset { min-width: 0; }
    .exam-decision-choice { display: flex; align-items: center; gap: 10px; padding: 9px 12px; margin-bottom: 7px; border: 1px solid #dfe7e2; border-radius: 8px; background: #fff; font-size: 13px; cursor: pointer; }
    .exam-decision-choice:has(input:checked) { border-color: #267151; background: #edf7f0; color: #164d37; font-weight: 600; }
    .exam-decision-choice .form-check-input { float: none; flex-shrink: 0; margin: 0; }
    .exam-decision-choice:focus-within { outline: 2px solid #267151; outline-offset: 2px; }
    .exam-flagged { border-color: #e8a6a6; }
    .exam-flagged .exam-participant-header { background: #fff2f2; }
    .exam-interview summary { cursor: pointer; color: #225d42; font-size: 13px; }
    @media (max-width: 1250px) {
        .exam-participant-body { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
        .exam-decision { grid-column: 1 / -1; border-top: 1px solid #e5ebe7; }
        .exam-panel.exam-decision { border-left: 0; }
    }
    @media (max-width: 767.98px) {
        .exam-page { padding: 0; }
        .exam-participant-body { grid-template-columns: minmax(0, 1fr); }
        .exam-panel + .exam-panel { border-left: 0; border-top: 1px solid #e5ebe7; }
        .exam-panel, .exam-participant-header { padding: 16px; }
    }
</style>
@endpush

@section('content')
    <div class="exam-page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-1">Ujian PPDB</h4>
            <div class="text-muted">Ujian Tahsin Lisan PPDB, ringkasan wawancara, dan keputusan kelulusan. Pilihan kelulusan tersimpan otomatis.</div>
        </div>
        <a href="{{ route('admin.registrations.assessments') }}" class="btn btn-outline-secondary">Nilai Seleksi</a>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="card trezo-card mb-3"><div class="card-body">
        <form method="GET" action="{{ route('admin.oral-exams.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label">Cari Peserta</label>
                <input id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Nama / nomor pendaftaran">
            </div>
            <div class="col-md-3">
                <label for="period_id" class="form-label">Periode</label>
                <select id="period_id" name="period_id" class="form-select">
                    <option value="">Semua Periode</option>
                    @foreach ($periods as $period)<option value="{{ $period->id }}" @selected((string) request('period_id') === (string) $period->id)>{{ $period->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="gender" class="form-label">Kelompok</label>
                <select id="gender" name="gender" class="form-select">
                    <option value="">Semua</option>
                    <option value="male" @selected(request('gender') === 'male')>Ikhwan</option>
                    <option value="female" @selected(request('gender') === 'female')>Akhwat</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="program" class="form-label">Program</label>
                <select id="program" name="program" class="form-select">
                    <option value="">Semua</option>
                    <option value="mahad" @selected(request('program') === 'mahad')>Ma'had</option>
                    <option value="takhosus" @selected(request('program') === 'takhosus')>Takhosus</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary">Cari</button>
                <a href="{{ route('admin.oral-exams.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div></div>
    <div class="row g-3 mb-3">
        @foreach ($quotaPeriods as $period)
            <div class="col-lg-4"><div class="card trezo-card h-100"><div class="card-body">
                <h6>{{ $period->name }}</h6>
                @foreach (['scholarship_quota' => 'Beasiswa', 'takhosus_ikhwan_quota' => 'Takhosus Ikhwan', 'takhosus_akhwat_quota' => 'Takhosus Akhwat'] as $field => $label)
                    <div>{{ $label }}: <strong data-quota-period="{{ $period->id }}" data-quota-field="{{ $field }}">{{ $quotaUsage[$period->id][$field] }}</strong> / {{ $period->{$field} }}</div>
                @endforeach
                <div class="small text-muted mt-2">Jumlah santri lulus / kuota periode.</div>
            </div></div></div>
        @endforeach
    </div>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h5 class="mb-0">Daftar Peserta <span class="badge bg-light text-dark ms-2">{{ $registrations->total() }}</span></h5>
        <span class="small text-muted">Keputusan disimpan otomatis</span>
    </div>
    @forelse ($registrations as $registration)
        @include('admin.oral-exams.participant')
    @empty
        <div class="card card-body text-center text-muted py-5">Belum ada peserta yang sesuai filter.</div>
    @endforelse
    <div class="mt-3">{{ $registrations->links() }}</div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.decision-options').forEach(fieldset => {
        fieldset.addEventListener('change', async event => {
            if (!event.target.matches('input[type="radio"]')) return;
            const selected = event.target.value;
            const message = fieldset.querySelector('.decision-message');
            fieldset.disabled = true;
            message.className = 'decision-message small mt-2 text-muted';
            message.textContent = 'Menyimpan...';
            try {
                const response = await fetch(fieldset.dataset.url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
                    body: JSON.stringify({ decision: selected })
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || data.message || 'Gagal menyimpan. Muat ulang halaman untuk memeriksa status.');
                fieldset.dataset.saved = data.decision;
                message.className = 'decision-message small mt-2 text-success';
                message.textContent = data.message;
                document.querySelectorAll('[data-quota-period]').forEach(counter => {
                    if (counter.dataset.quotaPeriod === String(data.period_id)) counter.textContent = data.usage[counter.dataset.quotaField];
                });
            } catch (error) {
                fieldset.querySelectorAll('input[type="radio"]').forEach(input => input.checked = input.value === fieldset.dataset.saved);
                message.className = 'decision-message small mt-2 text-danger';
                message.textContent = error.message;
            } finally {
                fieldset.disabled = false;
            }
        });
    });
});
</script>
@endpush
