<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu QR Pendaftar</title>
    <style>
        @page {
            margin: 0.65cm 0.75cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #173324;
            font-size: 9px;
        }

        .sheet-header {
            margin-bottom: 0.3cm;
            padding: 0.25cm 0.15cm 0.2cm;
            border-bottom: 1px solid #d9e7de;
        }

        .sheet-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .sheet-subtitle {
            font-size: 9px;
            color: #557062;
        }

        .cards-grid {
            font-size: 0;
        }

        .page {
            width: 100%;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .card {
            display: inline-block;
            vertical-align: top;
            width: 8.5cm;
            height: 5.4cm;
            margin-right: 0.35cm;
            margin-bottom: 0.35cm;
            border: 1px solid #cfe1d5;
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
            font-size: 9px;
            position: relative;
        }

        .card:nth-child(2n) {
            margin-right: 0;
        }

        .card.cut-right::after {
            content: "";
            position: absolute;
            top: 50%;
            right: -0.19cm;
            width: 0.12cm;
            border-top: 1px dashed #94aa9d;
        }

        .card.cut-bottom::before {
            content: "";
            position: absolute;
            left: 50%;
            bottom: -0.18cm;
            height: 0.12cm;
            border-left: 1px dashed #94aa9d;
        }

        .card-head {
            padding: 0.22cm 0.25cm;
            background: linear-gradient(135deg, #14532d 0%, #198754 60%, #8ec5a3 100%);
            color: #ffffff;
        }

        .brand-table,
        .body-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 0.95cm;
            vertical-align: top;
        }

        .logo {
            width: 0.72cm;
            height: 0.72cm;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 8px;
            padding: 3px;
        }

        .brand-name {
            font-size: 10px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1px;
        }

        .brand-meta {
            font-size: 7px;
            color: rgba(255, 255, 255, 0.9);
        }

        .card-body {
            padding: 0.2cm 0.24cm 0.22cm;
        }

        .qr-cell {
            width: 2.25cm;
            text-align: center;
            vertical-align: top;
        }

        .qr-box {
            border: 1px solid #d8e8de;
            border-radius: 8px;
            padding: 0.08cm;
            background: #f8fbf9;
        }

        .qr-box img {
            width: 1.7cm;
            height: 1.7cm;
        }

        .qr-note {
            margin-top: 2px;
            font-size: 6.5px;
            color: #667d71;
            line-height: 1.3;
        }

        .details-cell {
            vertical-align: top;
            padding-left: 0.16cm;
        }

        .registration-no {
            font-size: 9px;
            font-weight: 700;
            color: #14532d;
            margin-bottom: 0.06cm;
        }

        .student-name {
            font-size: 10px;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 0.08cm;
        }

        .detail-line {
            margin-bottom: 0.05cm;
            line-height: 1.3;
        }

        .detail-label {
            color: #607569;
        }

        .footer-line {
            margin-top: 0.08cm;
            padding-top: 0.08cm;
            border-top: 1px dashed #d8e8de;
            font-size: 6.5px;
            color: #607569;
        }

        .cut-guide-note {
            margin-top: 0.12cm;
            font-size: 7px;
            color: #6b8175;
            text-align: right;
        }
    </style>
</head>

<body>
    @foreach ($cards->chunk(10) as $pageCards)
        <div class="page">
            <div class="sheet-header">
                <div class="sheet-title">Kartu QR Pendaftar PPDB 2027/2028</div>
                <div class="sheet-subtitle">
                    Ma'had Darussalam Palas • Ukuran kartu 8,5 × 5,4 cm • Format cetak 2 kolom × 5 baris • Dicetak
                    {{ $printedAt->format('d M Y H:i') }}
                </div>
            </div>
            <div class="cards-grid">
                @foreach ($pageCards as $index => $card)
                    @php
                        $registration = $card['registration'];
                        $student = $card['student'];
                        $continuation = $card['continuation'] ?? null;
                        $position = $index + 1;
                        $isRightColumn = $position % 2 === 0;
                        $isLastRowOnPage = $position > 8;
                    @endphp
                    <div class="card {{ $isRightColumn ? '' : 'cut-right' }} {{ $isLastRowOnPage ? '' : 'cut-bottom' }}">
                        <div class="card-head">
                            <table class="brand-table">
                                <tr>
                                    <td class="logo-cell">
                                        <img src="{{ $card['logoImage'] }}" alt="Logo" class="logo">
                                    </td>
                                    <td>
                                        <div class="brand-name">Mahad Darussalam Palas</div>
                                        <div class="brand-meta">PPDB Tahun Ajaran 2027/2028</div>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="card-body">
                            <table class="body-table">
                                <tr>
                                    <td class="qr-cell">
                                        <div class="qr-box">
                                            <img src="{{ $card['qrImage'] }}" alt="QR {{ $registration->registration_no }}">
                                        </div>
                                        <div class="qr-note">Scan oleh admin untuk buka detail formulir</div>
                                    </td>
                                    <td class="details-cell">
                                        <div class="registration-no">{{ $registration->registration_no }}</div>
                                        <div class="student-name">{{ $student?->full_name ?? $continuation?->full_name ?? '-' }}</div>
                                        <div class="detail-line">
                                            <span class="detail-label">Asal Sekolah:</span> {{ $student?->school_origin ?? 'Darussalam' }}
                                        </div>
                                        <div class="detail-line">
                                            <span class="detail-label">Jalur:</span> {{ $registration->funding_type_label }}
                                        </div>
                                        <div class="detail-line">
                                            <span class="detail-label">JK:</span> {{ $registration->gender_label }}
                                        </div>
                                        <div class="detail-line">
                                            <span class="detail-label">Jenjang:</span> {{ $registration->education_level_label }}
                                        </div>
                                        <div class="footer-line">
                                            Status: {{ ucfirst($registration->status) }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="cut-guide-note">Garis putus-putus digunakan sebagai panduan potong kartu.</div>
        </div>
    @endforeach
</body>

</html>
