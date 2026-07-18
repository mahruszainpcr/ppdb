<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Pendaftaran {{ $registration->registration_no }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 10px;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #173324;
            background: #f4f8f5;
        }

        .sheet {
            background: #ffffff;
            border: 1px solid #d8e6dd;
            border-radius: 14px;
            overflow: hidden;
        }

        .hero {
            padding: 12px 14px 10px;
            color: #ffffff;
            background: linear-gradient(135deg, #14532d 0%, #198754 60%, #99ceb0 100%);
        }

        .hero-table,
        .content-table,
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 50px;
            vertical-align: top;
        }

        .logo {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            padding: 3px;
        }

        .eyebrow {
            margin: 0 0 2px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            color: #eef9f2;
        }

        .brand-name {
            margin: 0 0 2px;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.15;
        }

        .brand-address {
            margin: 0 0 2px;
            font-size: 8.4px;
            line-height: 1.3;
            color: rgba(255, 255, 255, 0.93);
        }

        .brand-meta {
            margin: 0;
            font-size: 8px;
            line-height: 1.25;
            color: rgba(255, 255, 255, 0.84);
        }

        .summary-chip {
            display: inline-block;
            margin-top: 7px;
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            background: rgba(255, 255, 255, 0.14);
            font-size: 7.8px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .summary-title {
            margin: 6px 0 2px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.15;
        }

        .summary-note {
            margin: 0;
            font-size: 8.4px;
            line-height: 1.32;
            color: rgba(255, 255, 255, 0.92);
        }

        .side-box {
            padding: 9px 10px 8px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.12);
            text-align: right;
        }

        .side-label {
            margin-bottom: 3px;
            font-size: 7.8px;
            color: rgba(255, 255, 255, 0.82);
        }

        .side-value {
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.15;
        }

        .side-value:last-child {
            margin-bottom: 0;
        }

        .content {
            padding: 12px 14px;
        }

        .qr-wrap {
            width: 180px;
            padding: 8px;
            text-align: center;
            border: 1px solid #d7e5dc;
            border-radius: 12px;
            background: #f8fbf9;
        }

        .qr-wrap img {
            width: 132px;
            height: 132px;
        }

        .qr-note {
            margin-top: 5px;
            font-size: 7.8px;
            line-height: 1.22;
            color: #5c7468;
        }

        .section-title {
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: 700;
            color: #173324;
        }

        .name-card {
            margin-bottom: 8px;
            padding: 10px;
            border: 1px solid #e2ede6;
            border-radius: 10px;
            background: #fbfdfc;
        }

        .label {
            margin-bottom: 3px;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .45px;
            text-transform: uppercase;
            color: #61776b;
        }

        .value {
            font-size: 13px;
            font-weight: 700;
            color: #173324;
        }

        .value.small {
            font-size: 10.6px;
            font-weight: 600;
        }

        .meta-table td {
            width: 50%;
            padding: 0 8px 7px 0;
            vertical-align: top;
        }

        .footer {
            padding: 0 14px 12px;
            font-size: 8px;
            line-height: 1.34;
            color: #5e7569;
        }

        .scan-link {
            font-size: 8px;
            color: #294b3b;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <div class="sheet">
        <div class="hero">
            <table class="hero-table">
                <tr>
                    <td style="width:72%; vertical-align:top;">
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td class="logo-cell">
                                    <img src="{{ $logoImage }}" alt="Logo Mahad" class="logo">
                                </td>
                                <td style="vertical-align:top;">
                                    <div class="eyebrow">Bukti pendaftaran</div>
                                    <div class="brand-name">Ma'had Darussalam Al-Islami</div>
                                    <div class="brand-address">Jl. Perjuangan, Kelurahan Palas, Kecamatan Rumbai, Pekanbaru, Riau</div>
                                    <div class="brand-meta">NPSN 70034877 • NSPP 510014710047</div>
                                </td>
                            </tr>
                        </table>

                        <div class="summary-chip">Dokumen Resmi PPDB</div>
                        <div class="summary-title">Bukti Pendaftaran Calon Santri</div>
                        <div class="summary-note">
                            Dokumen ini menjadi bukti pendaftaran dan dapat dipindai admin saat proses wawancara atau verifikasi.
                        </div>
                    </td>
                    <td style="width:28%; vertical-align:top;">
                        <div class="side-box">
                            <div class="side-label">Tahun Ajaran</div>
                            <div class="side-value">2027/2028</div>
                            <div class="side-label">No. Pendaftaran</div>
                            <div class="side-value">{{ $registration->registration_no }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="content">
            <table class="content-table">
                <tr>
                    <td style="width:29%; vertical-align:top; padding-right:12px;">
                        <div class="qr-wrap">
                            <img src="{{ $qrImage }}" alt="QR Pendaftaran">
                            <div class="qr-note">
                                Scan QR ini dengan akun admin yang sudah login untuk membuka detail pendaftaran.
                            </div>
                        </div>
                    </td>
                    <td style="width:71%; vertical-align:top;">
                        <div class="section-title">Data Inti Pendaftar</div>

                        <div class="name-card">
                            <div class="label">Nama Calon Santri</div>
                            <div class="value">{{ $student?->full_name ?? '-' }}</div>
                        </div>

                        <table class="meta-table">
                            <tr>
                                <td>
                                    <div class="label">Asal Sekolah</div>
                                    <div class="value small">{{ $student?->school_origin ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="label">Jenis Pendaftar</div>
                                    <div class="value small">{{ $registration->funding_type_label }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="label">Jenis Kelamin</div>
                                    <div class="value small">{{ $registration->gender_label }}</div>
                                </td>
                                <td>
                                    <div class="label">Jenjang</div>
                                    <div class="value small">{{ $registration->education_level_label }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="label">Status Pendaftaran</div>
                                    <div class="value small">{{ ucfirst($registration->status) }}</div>
                                </td>
                                <td>
                                    <div class="label">Tanggal Daftar</div>
                                    <div class="value small">{{ optional($registration->created_at)->format('d M Y H:i') ?? '-' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="label">Periode</div>
                                    <div class="value small">{{ $registration->period?->academic_year ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="label">Diunduh Pada</div>
                                    <div class="value small">{{ $downloadedAt->format('d M Y H:i') }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <div><strong>Link scan admin:</strong></div>
            <div class="scan-link">{{ $scanUrl }}</div>
            <div style="margin-top:6px;">
                Simpan dokumen ini sebagai bukti pendaftaran. QR code di atas hanya dapat membuka detail formulir melalui
                akun admin yang sudah login pada sistem PPDB.
            </div>
        </div>
    </div>
</body>

</html>
