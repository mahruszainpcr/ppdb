<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Formulir Lanjutan {{ $registration->registration_no }}</title>
    <style>
        body {
            margin: 0;
            padding: 26px 34px;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #111827;
        }

        .header-table,
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo {
            width: 70px;
            height: 70px;
        }

        .title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
        }

        .subtitle {
            margin: 3px 0 0;
            font-size: 14px;
            font-weight: 700;
            text-align: center;
        }

        .school {
            margin: 3px 0 0;
            font-size: 13px;
            text-align: center;
        }

        .year {
            margin: 3px 0 0;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
        }

        .section-title {
            margin: 16px 0 6px;
            font-size: 13px;
            font-weight: 700;
        }

        .field-table {
            width: 100%;
            border-collapse: collapse;
        }

        .field-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .field-label {
            width: 220px;
        }

        .check-row {
            margin: 4px 0;
        }

        .statement-box {
            margin-top: 8px;
            text-align: justify;
        }

        .signature-block {
            width: 44%;
            text-align: center;
            vertical-align: top;
        }

        .signature-image {
            width: 170px;
            height: 72px;
            object-fit: contain;
        }

        .signature-space {
            height: 72px;
        }

        .signature-name {
            margin-top: 8px;
            font-weight: 700;
        }

        .small-note {
            margin-top: 14px;
            font-size: 10px;
            color: #4b5563;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="width:80px;">
                @if ($logoImage)
                    <img src="{{ $logoImage }}" alt="Logo Mahad" class="logo">
                @endif
            </td>
            <td>
                <p class="title">Formulir Pendaftaran Lanjutan Santri</p>
                <p class="subtitle">Wustho ke Ulya</p>
                <p class="school">Ma'had Darussalam Al Islami</p>
                <p class="year">Tahun Ajaran {{ $registration->period?->academic_year ?? '2026/2027' }}</p>
            </td>
            <td style="width:80px;"></td>
        </tr>
    </table>

    <p class="section-title">A. Data Santri</p>
    <table class="field-table">
        <tr>
            <td class="field-label">1. Nama Lengkap</td>
            <td>: {{ $continuation?->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="field-label">2. Kelas Terakhir</td>
            <td>: {{ $continuation?->last_class ?? 'IX Wustho' }}</td>
        </tr>
        <tr>
            <td class="field-label">3. Jenis Kelamin</td>
            <td>: {{ $registration->gender === 'male' ? 'Ikhwan' : ($registration->gender === 'female' ? 'Akhwat' : '-') }}</td>
        </tr>
        <tr>
            <td class="field-label">4. Asrama</td>
            <td>: {{ $continuation?->dormitory ?? '-' }}</td>
        </tr>
    </table>

    <p class="section-title">B. Data Orang Tua / Wali</p>
    <table class="field-table">
        <tr>
            <td class="field-label">1. Nama Ayah/Wali</td>
            <td>: {{ $continuation?->father_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="field-label">2. Nomor HP/WhatsApp Ayah</td>
            <td>: {{ $continuation?->father_phone ?? '-' }}</td>
        </tr>
        <tr>
            <td class="field-label">3. Nama Ibu/Wali</td>
            <td>: {{ $continuation?->mother_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="field-label">4. Nomor HP/WhatsApp Ibu</td>
            <td>: {{ $continuation?->mother_phone ?? '-' }}</td>
        </tr>
    </table>

    <p class="section-title">C. Pilihan Melanjutkan</p>
    <div class="check-row">[{{ $continuation?->continue_to_ulya ? 'x' : ' ' }}] Bersedia melanjutkan pendidikan di Ulya
        Mahad Darussalam Al Islami.</div>

    <p class="section-title">D. Komitmen</p>
    <div class="check-row">[{{ $continuation?->agree_rules ? 'x' : ' ' }}] Bersedia menaati seluruh peraturan Mahad
        Darussalam Al Islami.</div>
    <div class="check-row">[{{ $continuation?->agree_programs ? 'x' : ' ' }}] Bersedia mengikuti seluruh program
        pembelajaran, tahfidz, bahasa Arab, diniyah, dan pembinaan yang berlaku di jenjang Ulya.</div>
    <div class="check-row">[{{ $continuation?->agree_administration ? 'x' : ' ' }}] Bersedia menyelesaikan administrasi
        sesuai ketentuan yang ditetapkan oleh Mahad (uang masuk sarpras: 1,5 juta, seragam dan buku 1,75 juta, SPP jalur
        mandiri 1,3 juta dan jalur beasiswa 650 ribu).</div>

    <p class="section-title">E. Konfirmasi Data</p>
    <div class="check-row">[{{ $continuation?->bedding_option === 'buy' ? 'x' : ' ' }}] Saya juga akan membeli kasur,
        seprei, bantal, dan lemari seharga 1,1 juta.</div>
    <div class="check-row">[{{ $continuation?->bedding_option === 'not_buy' ? 'x' : ' ' }}] Saya tidak membeli kasur,
        seprei, bantal, dan lemari.</div>

    <p class="section-title">Pernyataan</p>
    <div class="statement-box">
        Dengan ini saya menyatakan bahwa data yang saya berikan adalah benar dan saya mengajukan permohonan untuk
        melanjutkan pendidikan dari jenjang Wustho ke Ulya di Mahad Darussalam Al Islami. Saya tidak akan
        mengundurkan diri setelah menandatangani form ini.
    </div>

    <div style="margin-top: 24px;">Pekanbaru, {{ $downloadedAt->format('d F Y') }}</div>

    <table class="signature-table" style="margin-top: 18px;">
        <tr>
            <td class="signature-block">
                Orang Tua / Wali
            </td>
            <td style="width:12%;"></td>
            <td class="signature-block">
                Santri
            </td>
        </tr>
        <tr>
            <td class="signature-block">
                @if ($signatureImage)
                    <img src="{{ $signatureImage }}" alt="Tanda Tangan Orang Tua / Wali" class="signature-image">
                @else
                    <div class="signature-space"></div>
                @endif
            </td>
            <td></td>
            <td class="signature-block">
                <div class="signature-space"></div>
            </td>
        </tr>
        <tr>
            <td class="signature-block">
                <div class="signature-name">{{ $continuation?->father_name ?? $continuation?->mother_name ?? '-' }}</div>
            </td>
            <td></td>
            <td class="signature-block">
                <div class="signature-name">{{ $continuation?->full_name ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <div class="small-note">
        Nomor pendaftaran: {{ $registration->registration_no }} • Jenis pendaftar:
        {{ $registration->funding_type_label }} • Diunduh pada {{ $downloadedAt->format('d-m-Y H:i') }}
    </div>
</body>

</html>
