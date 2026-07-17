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
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #163020;
            margin: 0;
            padding: 24px;
            background: #f4f8f5;
        }

        .sheet {
            background: #ffffff;
            border: 1px solid #d7e5dc;
            border-radius: 18px;
            overflow: hidden;
        }

        .hero {
            padding: 24px 28px;
            color: #ffffff;
            background: linear-gradient(135deg, #14532d 0%, #198754 60%, #9bd3b2 100%);
        }

        .hero-table,
        .content-table,
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand-wrap {
            width: 100%;
        }

        .brand-logo-cell {
            width: 72px;
            vertical-align: top;
        }

        .brand-logo {
            width: 58px;
            height: 58px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            padding: 6px;
        }

        .brand-name {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .brand-subtitle {
            font-size: 11px;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.92);
        }

        .hero-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 6px;
        }

        .hero-subtitle {
            font-size: 12px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.92);
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-radius: 999px;
            font-size: 10px;
            letter-spacing: .8px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .content {
            padding: 28px;
        }

        .qr-box {
            width: 250px;
            border: 1px solid #d7e5dc;
            border-radius: 16px;
            padding: 14px;
            text-align: center;
            background: #f8fbf9;
        }

        .qr-box img {
            width: 190px;
            height: 190px;
        }

        .qr-note {
            margin-top: 10px;
            font-size: 10px;
            color: #557062;
            line-height: 1.5;
        }

        .summary-title {
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 14px;
            color: #163020;
        }

        .data-card {
            border: 1px solid #e3eee7;
            border-radius: 14px;
            padding: 16px;
            background: #fbfdfc;
        }

        .label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #5f7568;
            margin-bottom: 4px;
        }

        .value {
            font-size: 15px;
            font-weight: 700;
            color: #163020;
        }

        .value.small {
            font-size: 13px;
            font-weight: 600;
        }

        .meta-table td {
            width: 50%;
            padding: 0 8px 14px 0;
            vertical-align: top;
        }

        .footer {
            padding: 0 28px 24px;
            color: #567163;
            font-size: 10px;
            line-height: 1.7;
        }

        .scan-link {
            font-size: 10px;
            color: #2b4f3d;
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
                        <table class="brand-wrap" style="margin-bottom: 14px;">
                            <tr>
                                <td class="brand-logo-cell">
                                    <img src="https://mahaddarussalampalas.ponpes.id/logo.png" alt="Logo Mahad"
                                        class="brand-logo">
                                </td>
                                <td style="vertical-align: top;">
                                    <div class="brand-name">Mahad Darussalam Palas</div>
                                    <div class="brand-subtitle">
                                        Pondok pesantren berbasis Al-Qur'an, adab, dan pembinaan karakter santri.
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="badge">Bukti Pendaftaran Resmi PPDB</div>
                        <div class="hero-title">Bukti Pendaftaran Calon Santri</div>
                        <div class="hero-subtitle">
                            Dokumen ini menjadi bukti pendaftaran calon santri dan dapat dipindai oleh admin untuk
                            membuka detail formulir pendaftaran saat proses wawancara atau verifikasi.
                        </div>
                    </td>
                    <td style="width:28%; text-align:right; vertical-align:top;">
                        <div style="font-size:11px; color:rgba(255,255,255,.85); margin-bottom: 10px;">Tahun Ajaran</div>
                        <div style="font-size:18px; font-weight:700; margin-bottom: 12px;">2027/2028</div>
                        <div style="font-size:11px; color:rgba(255,255,255,.85);">No. Pendaftaran</div>
                        <div style="font-size:20px; font-weight:700;">{{ $registration->registration_no }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="content">
            <table class="content-table">
                <tr>
                    <td style="width:36%; vertical-align:top; padding-right:20px;">
                        <div class="qr-box">
                            <img src="{{ $qrImage }}" alt="QR Pendaftaran">
                            <div class="qr-note">
                                Scan QR ini dengan akun admin yang sudah login untuk membuka detail pendaftaran.
                            </div>
                        </div>
                    </td>
                    <td style="width:64%; vertical-align:top;">
                        <div class="summary-title">Data Inti Pendaftar</div>

                        <div class="data-card" style="margin-bottom:16px;">
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
            <div style="margin-top:10px;">
                Simpan dokumen ini sebagai bukti pendaftaran. QR code di atas hanya dapat membuka detail formulir melalui
                akun admin yang sudah login pada sistem PPDB.
            </div>
        </div>
    </div>
</body>

</html>
