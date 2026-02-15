@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Dashboard Admin</h4>
            <div class="text-muted">PPDB / PSB Mahad Darussalam</div>
        </div>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="btn btn-outline-light btn-sm">Logout</button>
        </form>
    </div>

    <div class="card trezo-card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label mb-1">Filter Periode</label>
                    <select name="period_id" class="form-select">
                        <option value="">Semua Periode</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @selected($periodId == $period->id)>
                                {{ $period->name }} (Gel. {{ $period->wave }}) @if ($period->is_active) - Aktif @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <button class="btn btn-primary">Terapkan</button>
                    <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-light ms-1">
                        Lihat Data Pendaftar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @php
        $statusDraft = $statusCounts['draft'] ?? 0;
        $statusSubmitted = $statusCounts['submitted'] ?? 0;
        $statusVerified = $statusCounts['verified'] ?? 0;
        $statusRevision = $statusCounts['revision_requested'] ?? 0;

        $genderSeries = [
            $genderCounts['male'] ?? 0,
            $genderCounts['female'] ?? 0,
        ];
        $genderLabelsArr = [
            $genderLabels['male'],
            $genderLabels['female'],
        ];

        $educationSeries = [
            $educationGrouped['SMP'] ?? 0,
            $educationGrouped['SMA'] ?? 0,
        ];
        $educationLabels = ['SMP', 'SMA'];

        $statusSeries = [];
        $statusLabelsArr = [];
        foreach ($statusLabels as $key => $label) {
            $statusLabelsArr[] = $label;
            $statusSeries[] = $statusCounts[$key] ?? 0;
        }

        $graduationSeries = [];
        $graduationLabelsArr = [];
        foreach ($graduationLabels as $key => $label) {
            $graduationLabelsArr[] = $label;
            $graduationSeries[] = $graduationCounts[$key] ?? 0;
        }

        $fundingSeries = [];
        $fundingLabelsArr = [];
        foreach ($fundingLabels as $key => $label) {
            $fundingLabelsArr[] = $label;
            $fundingSeries[] = $fundingCounts[$key] ?? 0;
        }

        $topSchoolsLabels = $topSchools->pluck('label')->values();
        $topSchoolsTotals = $topSchools->pluck('total')->values();

        $topCitiesLabels = $topCities->pluck('label')->values();
        $topCitiesTotals = $topCities->pluck('total')->values();

        $topDistrictsLabels = $topDistricts->pluck('label')->values();
        $topDistrictsTotals = $topDistricts->pluck('total')->values();
    @endphp

    <div class="row g-3">
        <div class="col-12 col-md-3">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="text-muted">Total Pendaftar</div>
                    <div class="fs-3 fw-semibold">{{ number_format($totalRegistrations) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="text-muted">Terkirim</div>
                    <div class="fs-4 fw-semibold">{{ number_format($statusSubmitted) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="text-muted">Terverifikasi</div>
                    <div class="fs-4 fw-semibold">{{ number_format($statusVerified) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="text-muted">Draft</div>
                    <div class="fs-4 fw-semibold">{{ number_format($statusDraft) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="text-muted">Revisi</div>
                    <div class="fs-4 fw-semibold">{{ number_format($statusRevision) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="text-muted">Pembayaran Upload</div>
                    <div class="fs-4 fw-semibold">{{ number_format($paymentUploaded) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="text-muted">Pembayaran Terverifikasi</div>
                    <div class="fs-4 fw-semibold">{{ number_format($paymentVerified) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="text-muted">Pembayaran Belum Upload</div>
                    <div class="fs-4 fw-semibold">{{ number_format($paymentPending) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12 col-lg-4">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Komposisi Ikhwan/Akhwat</div>
                    <div id="chartGender" style="height: 260px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Komposisi SMP/SMA</div>
                    <div id="chartEducation" style="height: 260px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Jenis Pembiayaan</div>
                    <div id="chartFunding" style="height: 260px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12 col-lg-6">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Status Pendaftaran</div>
                    <div id="chartStatus" style="height: 280px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Status Kelulusan</div>
                    <div id="chartGraduation" style="height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12 col-lg-6">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Pendaftar Terbanyak per Sekolah</div>
                    <div id="chartTopSchools" style="height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Pendaftar Terbanyak per Kota</div>
                    <div id="chartTopCities" style="height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card trezo-card h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Pendaftar Terbanyak per Kecamatan</div>
                    <div id="chartTopDistricts" style="height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const genderSeries = @json($genderSeries);
            const genderLabels = @json($genderLabelsArr);
            const educationSeries = @json($educationSeries);
            const educationLabels = @json($educationLabels);
            const statusSeries = @json($statusSeries);
            const statusLabels = @json($statusLabelsArr);
            const graduationSeries = @json($graduationSeries);
            const graduationLabels = @json($graduationLabelsArr);
            const fundingSeries = @json($fundingSeries);
            const fundingLabels = @json($fundingLabelsArr);

            const topSchoolsLabels = @json($topSchoolsLabels);
            const topSchoolsTotals = @json($topSchoolsTotals);
            const topCitiesLabels = @json($topCitiesLabels);
            const topCitiesTotals = @json($topCitiesTotals);
            const topDistrictsLabels = @json($topDistrictsLabels);
            const topDistrictsTotals = @json($topDistrictsTotals);

            const baseDonut = {
                chart: { type: 'donut', height: 260 },
                legend: { position: 'bottom' },
                dataLabels: { enabled: true },
            };

            new ApexCharts(document.querySelector('#chartGender'), {
                ...baseDonut,
                series: genderSeries,
                labels: genderLabels,
            }).render();

            new ApexCharts(document.querySelector('#chartEducation'), {
                ...baseDonut,
                series: educationSeries,
                labels: educationLabels,
            }).render();

            new ApexCharts(document.querySelector('#chartFunding'), {
                ...baseDonut,
                series: fundingSeries,
                labels: fundingLabels,
            }).render();

            new ApexCharts(document.querySelector('#chartStatus'), {
                chart: { type: 'bar', height: 280 },
                series: [{ name: 'Jumlah', data: statusSeries }],
                xaxis: { categories: statusLabels },
                plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
            }).render();

            new ApexCharts(document.querySelector('#chartGraduation'), {
                chart: { type: 'bar', height: 280 },
                series: [{ name: 'Jumlah', data: graduationSeries }],
                xaxis: { categories: graduationLabels },
                plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
            }).render();

            new ApexCharts(document.querySelector('#chartTopSchools'), {
                chart: { type: 'bar', height: 320 },
                series: [{ name: 'Jumlah', data: topSchoolsTotals }],
                xaxis: { categories: topSchoolsLabels },
                plotOptions: { bar: { horizontal: true, barHeight: '60%' } },
            }).render();

            new ApexCharts(document.querySelector('#chartTopCities'), {
                chart: { type: 'bar', height: 320 },
                series: [{ name: 'Jumlah', data: topCitiesTotals }],
                xaxis: { categories: topCitiesLabels },
                plotOptions: { bar: { horizontal: true, barHeight: '60%' } },
            }).render();

            new ApexCharts(document.querySelector('#chartTopDistricts'), {
                chart: { type: 'bar', height: 320 },
                series: [{ name: 'Jumlah', data: topDistrictsTotals }],
                xaxis: { categories: topDistrictsLabels },
                plotOptions: { bar: { horizontal: true, barHeight: '60%' } },
            }).render();
        });
    </script>
@endpush
