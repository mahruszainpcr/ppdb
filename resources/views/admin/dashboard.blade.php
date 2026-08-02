@extends('layouts.app')
@section('title', 'Dashboard PPDB')

@section('content')
    @php
        $periodLabel = $selectedPeriod?->name ?? 'Semua Periode';
        $waveLabel = $selectedPeriod ? 'Gelombang ' . $selectedPeriod->wave : 'Semua Gelombang';
        $rangeLabel = $selectedPeriod && $selectedPeriod->registration_open_date && $selectedPeriod->registration_close_date
            ? $selectedPeriod->registration_open_date->translatedFormat('d M Y') . ' - ' . $selectedPeriod->registration_close_date->translatedFormat('d M Y')
            : 'Seluruh rentang data';

        $statusDraft = (int) ($statusCounts['draft'] ?? 0);
        $statusSubmitted = (int) ($statusCounts['submitted'] ?? 0);
        $statusVerified = (int) ($statusCounts['verified'] ?? 0);
        $statusRevision = (int) ($statusCounts['revision_requested'] ?? 0);
        $statusLulus = (int) ($graduationCounts['lulus'] ?? 0);

        $metricCards = [
            ['label' => 'Total Pendaftar', 'value' => $totalRegistrations, 'percent' => 100, 'tone' => 'blue', 'mono' => 'TP'],
            ['label' => 'Draft', 'value' => $statusDraft, 'percent' => $totalRegistrations > 0 ? round(($statusDraft / $totalRegistrations) * 100, 2) : 0, 'tone' => 'sky', 'mono' => 'DR'],
            ['label' => 'Submitted', 'value' => $statusSubmitted, 'percent' => $totalRegistrations > 0 ? round(($statusSubmitted / $totalRegistrations) * 100, 2) : 0, 'tone' => 'teal', 'mono' => 'SB'],
            ['label' => 'Terverifikasi', 'value' => $statusVerified, 'percent' => $totalRegistrations > 0 ? round(($statusVerified / $totalRegistrations) * 100, 2) : 0, 'tone' => 'green', 'mono' => 'VF'],
            ['label' => 'Revisi', 'value' => $statusRevision, 'percent' => $totalRegistrations > 0 ? round(($statusRevision / $totalRegistrations) * 100, 2) : 0, 'tone' => 'orange', 'mono' => 'RV'],
            ['label' => 'Lulus Sementara', 'value' => $statusLulus, 'percent' => $totalRegistrations > 0 ? round(($statusLulus / $totalRegistrations) * 100, 2) : 0, 'tone' => 'violet', 'mono' => 'LS'],
        ];

        $educationSeries = [
            (int) ($educationCounts['SMP_NEW'] ?? 0),
            (int) ($educationCounts['SMA_NEW'] ?? 0),
            (int) ($educationCounts['SMA_OLD'] ?? 0),
        ];
        $educationLabelsArr = [
            $educationLabels['SMP_NEW'] ?? 'SMP Baru',
            $educationLabels['SMA_NEW'] ?? 'SMA Baru',
            $educationLabels['SMA_OLD'] ?? 'SMP Pindahan/Lama',
        ];

        $fundingSeries = [
            (int) ($fundingCounts['mandiri'] ?? 0),
            (int) ($fundingCounts['beasiswa'] ?? 0),
        ];
        $fundingLabelsArr = [
            $fundingLabels['mandiri'] ?? 'Mandiri',
            $fundingLabels['beasiswa'] ?? 'Beasiswa',
        ];

        $statusSeries = [
            $statusDraft,
            $statusSubmitted,
            $statusVerified,
            $statusRevision,
        ];
        $statusLabelsArr = [
            $statusLabels['draft'] ?? 'Draft',
            $statusLabels['submitted'] ?? 'Submitted',
            $statusLabels['verified'] ?? 'Verified',
            $statusLabels['revision_requested'] ?? 'Revision Requested',
        ];

        $genderSeries = [
            (int) ($genderCounts['male'] ?? 0),
            (int) ($genderCounts['female'] ?? 0),
        ];
        $genderLabelsArr = [
            $genderLabels['male'] ?? 'Laki-laki',
            $genderLabels['female'] ?? 'Perempuan',
        ];

        $programSeries = [
            (int) ($programCounts['mahad'] ?? 0),
            (int) ($programCounts['takhosus'] ?? 0),
        ];
        $programLabelsArr = [
            $programLabels['mahad'] ?? 'Mahad',
            $programLabels['takhosus'] ?? 'Takhosus',
        ];

        $quranSeries = [
            (int) ($quranReadingCounts['none'] ?? 0),
            (int) ($quranReadingCounts['iqro'] ?? 0),
            (int) ($quranReadingCounts['fluent'] ?? 0),
            (int) ($quranReadingCounts['fluent_tahsin'] ?? 0),
        ];
        $quranLabelsArr = [
            $quranReadingLabels['none'] ?? 'Pemula',
            $quranReadingLabels['iqro'] ?? 'Dasar',
            $quranReadingLabels['fluent'] ?? 'Menengah',
            $quranReadingLabels['fluent_tahsin'] ?? 'Lancar',
        ];

        $topSchoolRows = $topSchools->map(function ($row) {
            return [
                'school' => $row->school ?: 'Tidak diketahui',
                'city' => $row->city ?: '-',
                'total' => (int) $row->total,
            ];
        });
    @endphp

    <div class="ppdb-dashboard">
        <section class="ppdb-dashboard__hero">
            <div>
                <h1>Dashboard PPDB</h1>
                <p>Monitoring Pendaftaran Peserta Didik Baru</p>
            </div>

            <form method="GET" action="{{ route('admin.dashboard') }}" class="hero-filters">
                <div class="hero-filter">
                    <label>Periode</label>
                    <select name="period_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Periode</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @selected($periodId == $period->id)>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="hero-filter">
                    <label>Gelombang</label>
                    <div class="hero-filter__static">{{ $waveLabel }}</div>
                </div>
                <div class="hero-filter hero-filter--wide">
                    <label>Rentang Tanggal</label>
                    <div class="hero-filter__static">{{ $rangeLabel }}</div>
                </div>
                <div class="hero-account">
                    <div class="hero-account__avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                    <div>
                        <strong>{{ auth()->user()->name ?? 'Admin PPDB' }}</strong>
                        <span>{{ auth()->user()->role === 'ustadz' ? 'Ustadz' : 'Administrator' }}</span>
                    </div>
                </div>
            </form>
        </section>

        <section class="metric-grid">
            @foreach ($metricCards as $metric)
                <article class="metric-card metric-card--{{ $metric['tone'] }}">
                    <div class="metric-card__badge">{{ $metric['mono'] }}</div>
                    <div class="metric-card__body">
                        <span>{{ $metric['label'] }}</span>
                        <strong>{{ number_format($metric['value']) }}</strong>
                        <small>{{ rtrim(rtrim(number_format($metric['percent'], 2), '0'), ',') }}%</small>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="dashboard-layout dashboard-layout--wa">
            <article class="panel panel--wa-summary">
                <div class="panel__header panel__header--inline">
                    <div>
                        <h3>Template Chat WA Ringkasan Pendaftaran</h3>
                        <p>Ringkasan ini otomatis mengikuti periode yang dipilih pada filter dashboard.</p>
                    </div>
                    <div class="wa-period-chip">{{ $registrationSummary['period_label'] }}</div>
                </div>

                <div class="wa-summary-grid">
                    <div class="wa-summary-card">
                        <span>Total Pendaftar</span>
                        <strong>{{ number_format($registrationSummary['total']) }}</strong>
                    </div>
                    <div class="wa-summary-card wa-summary-card--green">
                        <span>Lengkap 100%</span>
                        <strong>{{ number_format($registrationSummary['complete_total']) }}</strong>
                    </div>
                    <div class="wa-summary-card wa-summary-card--orange">
                        <span>Belum Lengkap</span>
                        <strong>{{ number_format($registrationSummary['incomplete_total']) }}</strong>
                    </div>
                </div>

                <textarea id="waSummaryTemplate" class="form-control wa-summary-textarea" rows="12" readonly>{{ $registrationSummary['message'] }}</textarea>

                <div class="wa-summary-actions">
                    <button type="button" class="btn btn-outline-primary" id="copyWaSummaryBtn">Copy Template</button>
                    <a href="{{ $registrationSummary['wa_url'] }}" target="_blank" rel="noopener" class="btn btn-success">
                        Buka WhatsApp
                    </a>
                </div>
            </article>
        </section>

        <section class="dashboard-layout dashboard-layout--top">
            <article class="panel panel--trend">
                <div class="panel__header">
                    <div>
                        <h3>Tren Pendaftaran</h3>
                        <p>Pergerakan pendaftaran harian dalam periode aktif</p>
                    </div>
                </div>
                <div id="chartTrend" class="panel__chart panel__chart--lg"></div>
            </article>

            <article class="panel">
                <div class="panel__header">
                    <div>
                        <h3>Pendaftaran per Jenjang</h3>
                        <p>Komposisi calon santri</p>
                    </div>
                </div>
                <div id="chartEducation" class="panel__chart"></div>
            </article>

            <article class="panel">
                <div class="panel__header">
                    <div>
                        <h3>Jenis Pendanaan</h3>
                        <p>Jalur pembiayaan pendaftar</p>
                    </div>
                </div>
                <div id="chartFunding" class="panel__chart"></div>
            </article>

            <article class="panel">
                <div class="panel__header">
                    <div>
                        <h3>Status Pendaftaran</h3>
                        <p>Distribusi status funnel</p>
                    </div>
                </div>
                <div id="chartStatus" class="panel__chart"></div>
            </article>

            <article class="panel panel--completion">
                <div class="panel__header">
                    <div>
                        <h3>Kelengkapan Dokumen</h3>
                        <p>Rata-rata verifikasi dokumen</p>
                    </div>
                </div>
                <div class="completion-ring" style="--completion: {{ $documentCompletionPercent }}%;">
                    <div>
                        <strong>{{ $documentCompletionPercent }}%</strong>
                        <span>Rata-rata Kelengkapan</span>
                    </div>
                </div>
                <div class="completion-meta">
                    <span>Total Dokumen Terverifikasi</span>
                    <strong>{{ number_format($documentVerifiedTotal) }} / {{ number_format($documentRequiredTotal) }}</strong>
                </div>
            </article>
        </section>

        <section class="dashboard-layout dashboard-layout--middle">
            <article class="panel panel--wide">
                <div class="panel__header">
                    <div>
                        <h3>Dokumen Wajib</h3>
                        <p>Status upload dan verifikasi tiap dokumen</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table dashboard-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Dokumen</th>
                                <th>Wajib</th>
                                <th>Upload</th>
                                <th>Verifikasi</th>
                                <th>%</th>
                                <th style="width: 25%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documentMetrics as $doc)
                                <tr>
                                    <td>{{ $doc['label'] }}</td>
                                    <td>{{ number_format($doc['required']) }}</td>
                                    <td>{{ number_format($doc['uploaded']) }}</td>
                                    <td>{{ number_format($doc['verified']) }}</td>
                                    <td>{{ $doc['percent'] }}%</td>
                                    <td>
                                        <div class="mini-progress">
                                            <div class="mini-progress__bar" style="width: {{ min($doc['percent'], 100) }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel">
                <div class="panel__header">
                    <div>
                        <h3>Jenis Kelamin</h3>
                        <p>Komposisi pendaftar</p>
                    </div>
                </div>
                <div id="chartGender" class="panel__chart"></div>
            </article>

            <article class="panel">
                <div class="panel__header">
                    <div>
                        <h3>Pilihan Program</h3>
                        <p>Minat program santri</p>
                    </div>
                </div>
                <div id="chartProgram" class="panel__chart"></div>
            </article>

            <article class="panel">
                <div class="panel__header">
                    <div>
                        <h3>Level Bacaan Al-Qur'an</h3>
                        <p>Kesiapan bacaan calon santri</p>
                    </div>
                </div>
                <div id="chartQuran" class="panel__chart"></div>
            </article>
        </section>

        <section class="dashboard-layout dashboard-layout--bottom">
            <article class="panel panel--map">
                <div class="panel__header">
                    <div>
                        <h3>Sebaran Pendaftar</h3>
                        <p>Visualisasi kota/kabupaten teratas</p>
                    </div>
                </div>
                <div class="map-card">
                    <div class="map-card__canvas">
                        @foreach ($mapPoints as $point)
                            <div class="map-bubble"
                                style="top: {{ $point['top'] }}; left: {{ $point['left'] }}; width: {{ $point['size'] }}px; height: {{ $point['size'] }}px;">
                                <span>{{ $point['total'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="map-card__legend">
                        @foreach ($cityPercentageRows as $city)
                            <div class="map-card__legend-item">
                                <span>{{ $city['label'] }}</span>
                                <strong>{{ $city['total'] }} ({{ $city['percent'] }}%)</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>

            <article class="panel">
                <div class="panel__header">
                    <div>
                        <h3>Asal Sekolah Teratas</h3>
                        <p>Sekolah penyumbang pendaftar terbanyak</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table dashboard-table mb-0">
                        <thead>
                            <tr>
                                <th>Asal Sekolah</th>
                                <th>Kota/Kabupaten</th>
                                <th>Pendaftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topSchoolRows as $row)
                                <tr>
                                    <td>{{ $row['school'] }}</td>
                                    <td>{{ $row['city'] }}</td>
                                    <td>{{ number_format($row['total']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-muted">Belum ada data sekolah.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel">
                <div class="panel__header">
                    <div>
                        <h3>Kota/Kabupaten Teratas</h3>
                        <p>Konsentrasi wilayah pendaftar</p>
                    </div>
                </div>
                <div class="region-list">
                    @forelse ($cityPercentageRows as $city)
                        <div class="region-list__item">
                            <div class="region-list__head">
                                <span>{{ $city['label'] }}</span>
                                <strong>{{ $city['percent'] }}%</strong>
                            </div>
                            <div class="region-list__meta">
                                <small>{{ number_format($city['total']) }} pendaftar</small>
                            </div>
                            <div class="mini-progress">
                                <div class="mini-progress__bar mini-progress__bar--blue" style="width: {{ min($city['percent'], 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada data kota/kabupaten.</div>
                    @endforelse
                </div>
            </article>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .ppdb-dashboard {
            --panel-bg: #ffffff;
            --panel-border: rgba(226, 232, 240, 0.9);
            --panel-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            --text-main: #0f172a;
            --text-soft: #64748b;
            --blue: #2f80ed;
            --teal: #22c7b8;
            --green: #30b566;
            --orange: #f59e0b;
            --violet: #8b5cf6;
            --pink: #ec4899;
            --slate-bg: #f8fbff;
            color: var(--text-main);
        }

        .ppdb-dashboard__hero {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .ppdb-dashboard__hero h1 {
            margin: 0;
            font-size: clamp(1.8rem, 2.2vw, 2.6rem);
            font-weight: 800;
        }

        .ppdb-dashboard__hero p {
            margin: 6px 0 0;
            color: var(--text-soft);
            font-size: 1rem;
        }

        .hero-filters {
            display: grid;
            grid-template-columns: 180px 170px 260px auto;
            gap: 14px;
            align-items: center;
        }

        .hero-filter,
        .hero-account {
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            box-shadow: var(--panel-shadow);
            border-radius: 18px;
            padding: 12px 14px;
        }

        .hero-filter label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-soft);
            margin-bottom: 8px;
        }

        .hero-filter .form-select,
        .hero-filter__static {
            min-height: 44px;
            border-radius: 12px;
            border-color: #dbe3ef;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .hero-filter__static {
            background: #f8fbff;
            padding: 0 12px;
        }

        .hero-account {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 210px;
        }

        .hero-account__avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0f3a2b, #2f80ed);
            color: #fff;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .hero-account strong,
        .hero-account span {
            display: block;
        }

        .hero-account span {
            color: var(--text-soft);
            font-size: 13px;
        }

        .metric-grid,
        .dashboard-layout {
            display: grid;
            gap: 16px;
        }

        .metric-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .metric-card,
        .panel {
            background: var(--panel-bg);
            border: 1px solid var(--panel-border);
            box-shadow: var(--panel-shadow);
            border-radius: 22px;
        }

        .metric-card {
            display: flex;
            gap: 14px;
            align-items: center;
            padding: 18px;
            min-height: 108px;
        }

        .metric-card__badge {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        .metric-card--blue .metric-card__badge { background: linear-gradient(135deg, #2563eb, #60a5fa); }
        .metric-card--sky .metric-card__badge { background: linear-gradient(135deg, #3b82f6, #93c5fd); }
        .metric-card--teal .metric-card__badge { background: linear-gradient(135deg, #14b8a6, #5eead4); }
        .metric-card--green .metric-card__badge { background: linear-gradient(135deg, #16a34a, #4ade80); }
        .metric-card--orange .metric-card__badge { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .metric-card--violet .metric-card__badge { background: linear-gradient(135deg, #8b5cf6, #c4b5fd); }

        .metric-card__body span,
        .metric-card__body small {
            display: block;
        }

        .metric-card__body span {
            color: var(--text-soft);
            font-size: 14px;
            margin-bottom: 2px;
        }

        .metric-card__body strong {
            display: block;
            font-size: 2rem;
            line-height: 1.05;
            margin-bottom: 4px;
        }

        .metric-card__body small {
            color: #334155;
            font-weight: 600;
        }

        .dashboard-layout--top {
            grid-template-columns: 2.1fr 1fr 1fr 1.15fr 0.95fr;
            margin-bottom: 16px;
        }

        .dashboard-layout--wa {
            grid-template-columns: 1fr;
            margin-bottom: 16px;
        }

        .dashboard-layout--middle {
            grid-template-columns: 2.2fr 1fr 1fr 1fr;
            margin-bottom: 16px;
        }

        .dashboard-layout--bottom {
            grid-template-columns: 2fr 1.1fr 1.1fr;
        }

        .panel {
            padding: 18px 18px 16px;
        }

        .panel__header {
            margin-bottom: 14px;
        }

        .panel__header h3 {
            margin: 0 0 4px;
            font-size: 1rem;
            font-weight: 800;
        }

        .panel__header p {
            margin: 0;
            color: var(--text-soft);
            font-size: 13px;
        }

        .panel__header--inline {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
        }

        .panel__chart {
            min-height: 260px;
        }

        .panel__chart--lg {
            min-height: 310px;
        }

        .completion-ring {
            --completion: 0%;
            width: 220px;
            height: 220px;
            margin: 8px auto 14px;
            border-radius: 50%;
            background:
                radial-gradient(closest-side, #fff 72%, transparent 73% 100%),
                conic-gradient(var(--blue) var(--completion), #e6edf8 0);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .completion-ring strong,
        .completion-ring span {
            display: block;
        }

        .completion-ring strong {
            font-size: 2.25rem;
            line-height: 1;
        }

        .completion-ring span {
            color: var(--text-soft);
            font-size: 13px;
            margin-top: 6px;
        }

        .completion-meta {
            text-align: center;
        }

        .completion-meta span,
        .completion-meta strong {
            display: block;
        }

        .completion-meta span {
            color: var(--text-soft);
            font-size: 13px;
        }

        .completion-meta strong {
            color: var(--blue);
            font-size: 1.35rem;
            margin-top: 4px;
        }

        .wa-period-chip {
            padding: 10px 14px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .wa-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 14px;
        }

        .wa-summary-card {
            padding: 16px 18px;
            border-radius: 18px;
            background: linear-gradient(135deg, #eff6ff, #f8fbff);
            border: 1px solid #dbeafe;
        }

        .wa-summary-card--green {
            background: linear-gradient(135deg, #ecfdf5, #f7fee7);
            border-color: #bbf7d0;
        }

        .wa-summary-card--orange {
            background: linear-gradient(135deg, #fff7ed, #fffbeb);
            border-color: #fed7aa;
        }

        .wa-summary-card span,
        .wa-summary-card strong {
            display: block;
        }

        .wa-summary-card span {
            color: var(--text-soft);
            font-size: 13px;
            margin-bottom: 6px;
        }

        .wa-summary-card strong {
            font-size: 1.9rem;
            line-height: 1;
        }

        .wa-summary-textarea {
            min-height: 320px;
            border-radius: 18px;
            border-color: #dbe3ef;
            background: #f8fbff;
            resize: vertical;
            margin-bottom: 14px;
            font-size: 13px;
            line-height: 1.6;
        }

        .wa-summary-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .dashboard-table thead th {
            font-size: 12px;
            color: var(--text-soft);
            font-weight: 700;
            background: #f8fbff;
            border-bottom-color: #e6edf8;
        }

        .dashboard-table tbody td {
            font-size: 13px;
            color: #1e293b;
            border-bottom-color: #edf2f7;
        }

        .mini-progress {
            width: 100%;
            height: 8px;
            background: #e7eef8;
            border-radius: 999px;
            overflow: hidden;
        }

        .mini-progress__bar {
            height: 100%;
            background: linear-gradient(90deg, #22c55e, #34d399);
            border-radius: inherit;
        }

        .mini-progress__bar--blue {
            background: linear-gradient(90deg, #2563eb, #60a5fa);
        }

        .map-card {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 14px;
            align-items: center;
            min-height: 250px;
        }

        .map-card__canvas {
            position: relative;
            min-height: 250px;
            border-radius: 20px;
            background:
                radial-gradient(circle at 20% 30%, rgba(96, 165, 250, 0.10), transparent 18%),
                radial-gradient(circle at 75% 55%, rgba(59, 130, 246, 0.10), transparent 22%),
                linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
            overflow: hidden;
        }

        .map-card__canvas::before {
            content: '';
            position: absolute;
            inset: 8% 6%;
            background: linear-gradient(135deg, rgba(203, 213, 225, 0.55), rgba(226, 232, 240, 0.1));
            clip-path: polygon(3% 38%, 12% 18%, 26% 12%, 38% 22%, 52% 16%, 70% 20%, 83% 35%, 98% 43%, 88% 58%, 74% 62%, 67% 79%, 46% 70%, 30% 82%, 14% 68%, 7% 52%);
            border-radius: 26px;
        }

        .map-bubble {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #60a5fa, #2563eb);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.28);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: translate(-50%, -50%);
        }

        .map-bubble span {
            font-size: 12px;
            font-weight: 800;
        }

        .map-card__legend {
            display: grid;
            gap: 10px;
        }

        .map-card__legend-item {
            padding: 10px 12px;
            border-radius: 16px;
            background: #f8fbff;
            border: 1px solid #e6edf8;
        }

        .map-card__legend-item span,
        .map-card__legend-item strong {
            display: block;
        }

        .map-card__legend-item span {
            color: var(--text-soft);
            font-size: 12px;
            margin-bottom: 2px;
        }

        .region-list {
            display: grid;
            gap: 12px;
        }

        .region-list__item {
            padding: 12px 14px;
            border-radius: 16px;
            background: #f8fbff;
            border: 1px solid #e6edf8;
        }

        .region-list__head,
        .region-list__meta {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .region-list__head {
            margin-bottom: 4px;
        }

        .region-list__head span {
            font-weight: 600;
        }

        .region-list__meta {
            margin-bottom: 10px;
        }

        .region-list__meta small {
            color: var(--text-soft);
        }

        @media (max-width: 1600px) {
            .metric-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .dashboard-layout--top {
                grid-template-columns: 1.6fr 1fr 1fr;
            }

            .dashboard-layout--middle {
                grid-template-columns: 1.5fr 1fr 1fr;
            }

            .dashboard-layout--bottom {
                grid-template-columns: 1fr 1fr;
            }

            .panel--completion,
            .panel--map {
                grid-column: span 1;
            }
        }

        @media (max-width: 1200px) {
            .ppdb-dashboard__hero,
            .dashboard-layout--top,
            .dashboard-layout--middle,
            .dashboard-layout--bottom {
                display: grid;
                grid-template-columns: 1fr;
            }

            .hero-filters {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .metric-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .wa-summary-grid {
                grid-template-columns: 1fr;
            }

            .map-card {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .hero-filters,
            .metric-grid {
                grid-template-columns: 1fr;
            }

            .metric-card,
            .panel,
            .hero-filter,
            .hero-account {
                border-radius: 18px;
            }

            .panel__header--inline,
            .wa-summary-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .completion-ring {
                width: 180px;
                height: 180px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const trendLabels = @json($trendLabels);
            const trendSeries = @json($trendSeries);
            const educationSeries = @json($educationSeries);
            const educationLabels = @json($educationLabelsArr);
            const fundingSeries = @json($fundingSeries);
            const fundingLabels = @json($fundingLabelsArr);
            const statusSeries = @json($statusSeries);
            const statusLabels = @json($statusLabelsArr);
            const genderSeries = @json($genderSeries);
            const genderLabels = @json($genderLabelsArr);
            const programSeries = @json($programSeries);
            const programLabels = @json($programLabelsArr);
            const quranSeries = @json($quranSeries);
            const quranLabels = @json($quranLabelsArr);
            const copyWaSummaryBtn = document.getElementById('copyWaSummaryBtn');
            const waSummaryTemplate = document.getElementById('waSummaryTemplate');

            if (copyWaSummaryBtn && waSummaryTemplate) {
                copyWaSummaryBtn.addEventListener('click', async function () {
                    try {
                        await navigator.clipboard.writeText(waSummaryTemplate.value);
                        copyWaSummaryBtn.textContent = 'Template Tersalin';

                        window.setTimeout(function () {
                            copyWaSummaryBtn.textContent = 'Copy Template';
                        }, 1800);
                    } catch (error) {
                        waSummaryTemplate.removeAttribute('readonly');
                        waSummaryTemplate.select();
                        document.execCommand('copy');
                        waSummaryTemplate.setAttribute('readonly', 'readonly');
                        copyWaSummaryBtn.textContent = 'Template Tersalin';

                        window.setTimeout(function () {
                            copyWaSummaryBtn.textContent = 'Copy Template';
                        }, 1800);
                    }
                });
            }

            const axisColor = '#64748b';
            const gridColor = 'rgba(148, 163, 184, 0.18)';

            new ApexCharts(document.querySelector('#chartTrend'), {
                chart: {
                    type: 'line',
                    height: 310,
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                series: [{
                    name: 'Pendaftar',
                    data: trendSeries
                }],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#2563eb'],
                markers: {
                    size: 4,
                    strokeWidth: 0,
                    hover: { sizeOffset: 2 }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 5
                },
                xaxis: {
                    categories: trendLabels,
                    labels: {
                        style: { colors: axisColor }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: axisColor }
                    }
                },
                legend: { show: false },
                tooltip: { theme: 'light' }
            }).render();

            const donutBase = {
                chart: { type: 'donut', height: 260 },
                legend: { position: 'right' },
                stroke: { width: 0 },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%'
                        }
                    }
                }
            };

            new ApexCharts(document.querySelector('#chartEducation'), {
                ...donutBase,
                series: educationSeries,
                labels: educationLabels,
                colors: ['#2563eb', '#22c7b8', '#a78bfa']
            }).render();

            new ApexCharts(document.querySelector('#chartFunding'), {
                ...donutBase,
                series: fundingSeries,
                labels: fundingLabels,
                colors: ['#2563eb', '#22c7b8']
            }).render();

            new ApexCharts(document.querySelector('#chartGender'), {
                ...donutBase,
                series: genderSeries,
                labels: genderLabels,
                colors: ['#2563eb', '#ec4899']
            }).render();

            new ApexCharts(document.querySelector('#chartProgram'), {
                ...donutBase,
                series: programSeries,
                labels: programLabels,
                colors: ['#2563eb', '#22c7b8']
            }).render();

            new ApexCharts(document.querySelector('#chartStatus'), {
                chart: {
                    type: 'bar',
                    height: 260,
                    toolbar: { show: false }
                },
                series: [{
                    name: 'Jumlah',
                    data: statusSeries
                }],
                colors: ['#60a5fa', '#22c7b8', '#30b566', '#f59e0b'],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 8,
                        distributed: true,
                        barHeight: '58%'
                    }
                },
                dataLabels: { enabled: false },
                legend: { show: false },
                xaxis: {
                    categories: statusLabels,
                    labels: {
                        style: { colors: axisColor }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: axisColor }
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4
                }
            }).render();

            new ApexCharts(document.querySelector('#chartQuran'), {
                chart: {
                    type: 'bar',
                    height: 260,
                    toolbar: { show: false }
                },
                series: [{
                    name: 'Jumlah',
                    data: quranSeries
                }],
                colors: ['#3b82f6'],
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        columnWidth: '44%'
                    }
                },
                dataLabels: { enabled: true },
                xaxis: {
                    categories: quranLabels,
                    labels: {
                        style: { colors: axisColor }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: axisColor }
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4
                }
            }).render();
        });
    </script>
@endpush
