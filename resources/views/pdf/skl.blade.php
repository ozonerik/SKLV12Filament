<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #000; line-height: 1.25; margin: 0; padding-bottom: 4cm; }
        .center { text-align: center; }
        .kop { width: 100%; border-bottom: 2px solid #000; margin-bottom: 6px; }
        .kop td { vertical-align: top; }
        .logo { width: 72px; height: 72px; object-fit: contain; }
        .kop-top { font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .kop-school { font-size: 34px; font-weight: 700; text-transform: uppercase; line-height: 1.05; }
        .kop-address { font-size: 10px; }
        .doc-title { margin-top: 8px; font-size: 31px; font-weight: 700; text-transform: uppercase; text-decoration: underline; }
        .doc-number { font-size: 15px; margin-top: 2px; }
        .paragraph { margin: 5px 0; }
        .identity { width: 100%; margin-top: 4px; }
        .identity td { padding: 0; vertical-align: top; }
        .identity .label { width: 180px; }
        .identity .colon { width: 10px; }
        .status { text-align: center; font-size: 20px; font-weight: 700; text-transform: uppercase; margin: 1px 0 2px; }
        .grade-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .grade-table th, .grade-table td { border: 1px solid #000; padding: 2px 5px; }
        .grade-table thead th { text-align: center; font-weight: 700; }
        .group-row td { font-weight: 700; background: #f5f5f5; }
        .num { width: 34px; text-align: center; }
        .score { width: 64px; text-align: center; }
        .footer-note { margin-top: 6px; }
        .footer-grid { width: 100%; margin-top: 6px; }
        .footer-grid td { vertical-align: top; }
        .verification-box { width: 180px; font-size: 9px; }
        .verification-qr { width: 58px; height: 58px; border: 1px solid #000; padding: 2px; }
        .sign-box { text-align: center; }
        .sign-right { text-align: right; }
        .footer-verification {
            position: fixed;
            bottom: 1cm;
            left: 0;
            right: 0;
            border-top: 1px solid #999;
            padding-top: 6px;
        }
        .signature-wrapper { position: relative; height: 3.5cm; margin: 0; overflow: visible; }
        .signature-image {
            position: absolute;
            left: calc(50% - 0.5cm);
            top: 0.45cm;
            width: auto;
            height: 2.5cm;
            z-index: 2;
        }
        .stamp-image {
            position: absolute;
            left: calc(50% - 3.5cm);
            top: 0.15cm;
            width: auto;
            height: 4cm;
            z-index: 3;
            opacity: 0.9;
        }
        .name-line { font-weight: 700; text-decoration: underline; margin-top: -0.75cm; position: relative; z-index: 1; }
        .muted { color: #111; font-size: 9px; }
    </style>
</head>
<body>
    @php
        $majorName = $major?->konsentrasi_keahlian ?? '-';
        $programKeahlian = $major?->program_keahlian ?? '-';
        $bidangKeahlian = $major?->bidang_keahlian ?? '-';
        $city = $school?->city ?? 'Indramayu';

        $orderedCategories = [
            'Umum' => '1. Kelompok Mapel Umum',
            'Kejuruan' => '2. Kelompok Mapel Kejuruan',
            'Pilihan' => '3. Kelompok Mapel Pilihan',
            'Mulok' => '4. Kelompok Mapel Mulok',
        ];
    @endphp

    <table class="kop">
        <tr>
            <td style="width: 80px; text-align: left;">
                @if (! empty($school?->province_logo))
                    <img src="{{ public_path('storage/' . $school->province_logo) }}" alt="Logo Provinsi" class="logo">
                @endif
            </td>
            <td class="center">
                <div class="kop-top">Pemerintah Provinsi {{ strtoupper((string) ($school?->province ?? '-')) }}</div>
                <div class="kop-top">Dinas Pendidikan</div>
                <div class="kop-top">Kantor Cabang Dinas Pendidikan Wilayah {{ strtoupper((string) ($school?->kcd_wilayah ?? '-')) }}</div>
                <div class="kop-school">{{ strtoupper((string) ($school?->name ?? 'SMK NEGERI')) }}</div>
                <div class="kop-address">Alamat: {{ $school?->address ?? '-' }}, {{ $school?->postal_code ?? '-' }}</div>
                <div class="kop-address">
                    @if (! empty($school?->phone))
                        Telp. {{ $school->phone }}
                    @endif
                    @if (! empty($school?->email))
                        | Email. {{ $school->email }}
                    @endif
                    @if (! empty($school?->website))
                        | Website: {{ $school->website }}
                    @endif
                </div>
            </td>
            <td style="width: 80px;"></td>
        </tr>
    </table>

    <div class="center doc-title">Surat Keterangan Lulus</div>
    <div class="center doc-number">Nomor: {{ $skl->letter_number }}</div>

    <p class="paragraph">
        Yang bertanda tangan di bawah ini Kepala {{ $school?->name ?? 'Sekolah' }}, {{ $city }},
        {{ $school?->province ?? '-' }}, dengan ini menerangkan:
    </p>

    <table class="identity">
        <tr><td class="label">Nama</td><td class="colon">:</td><td><strong>{{ strtoupper((string) ($student?->name ?? '-')) }}</strong></td></tr>
        <tr><td class="label">Tempat, Tanggal Lahir</td><td class="colon">:</td><td>{{ $student?->pob ?? '-' }}, {{ $student?->dob ? \Carbon\Carbon::parse($student->dob)->translatedFormat('d F Y') : '-' }}</td></tr>
        <tr><td class="label">Nomor Induk Peserta Didik</td><td class="colon">:</td><td>{{ $student?->nis ?? '-' }}</td></tr>
        <tr><td class="label">Nomor Induk Siswa Nasional</td><td class="colon">:</td><td>{{ $student?->nisn ?? '-' }}</td></tr>
        <tr><td class="label">Bidang Keahlian</td><td class="colon">:</td><td>{{ $bidangKeahlian }}</td></tr>
        <tr><td class="label">Program Keahlian</td><td class="colon">:</td><td>{{ $programKeahlian }}</td></tr>
        <tr><td class="label">Konsentrasi Keahlian</td><td class="colon">:</td><td>{{ $majorName }}</td></tr>
    </table>

    <p class="paragraph">Berdasarkan kriteria kelulusan peserta didik yang sudah ditetapkan, maka yang bersangkutan dinyatakan:</p>
    <div class="status">{{ $skl->status }}</div>
    <p class="paragraph">Dengan hasil sebagai berikut:</p>

    <table class="grade-table">
        <thead>
            <tr>
                <th class="num">No.</th>
                <th>Mata Pelajaran</th>
                <th class="score">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orderedCategories as $category => $categoryLabel)
                @php
                    $categoryGrades = $groupedGrades[$category] ?? collect();
                @endphp

                <tr class="group-row">
                    <td colspan="3">{{ $categoryLabel }}</td>
                </tr>

                @if ($categoryGrades->isEmpty())
                    <tr>
                        <td class="num">-</td>
                        <td>Tidak ada data mata pelajaran</td>
                        <td class="score">-</td>
                    </tr>
                @else
                    @foreach($categoryGrades as $idx => $grade)
                        <tr>
                            <td class="num">{{ $idx + 1 }}</td>
                            <td>{{ $grade->subject?->name ?? '-' }}</td>
                            <td class="score">{{ number_format((float) $grade->score, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td class="num">-</td>
                    <td>Belum ada data nilai.</td>
                    <td class="score">-</td>
                </tr>
            @endforelse

            <tr class="group-row">
                <td colspan="2" style="text-align: right;">Rata-rata</td>
                <td class="score">{{ number_format((float) ($averageScore ?? 0), 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p class="footer-note">
        Surat Keterangan ini bersifat sementara dan berlaku sampai diterbitkannya ijazah untuk peserta didik yang bersangkutan.
        Demikian surat keterangan ini diberikan agar dapat dipergunakan sebagaimana mestinya.
    </p>

    <table class="footer-grid">
        <tr>
            <td style="width: 60%;"></td>
            <td class="sign-box" style="width: 40%; text-align: center;">
                <div>{{ $city }}, {{ $skl->letter_date ? \Carbon\Carbon::parse($skl->letter_date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</div>
                <div>Kepala Sekolah,</div>

                <div class="signature-wrapper">
                    @if($headmaster && $headmaster->ttd)
                        <img src="{{ public_path('storage/' . $headmaster->ttd) }}" class="signature-image" alt="TTD Kepala Sekolah">
                    @endif
                    @if (! empty($school?->school_stamp))
                        <img src="{{ public_path('storage/' . $school->school_stamp) }}" alt="Stamp Sekolah" class="stamp-image">
                    @endif
                </div>

                <div class="name-line">{{ $headmaster?->name ?? '...........................................' }}</div>
                <div>NIP. {{ $headmaster?->nip ?? '-' }}</div>
                <div>{{ $headmaster?->rank ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <table class="footer-verification">
        <tr>
            <td style="width: 90px; vertical-align: top;">
                <img src="{{ $qrCodeDataUri }}" alt="QR Verifikasi SKL" class="verification-qr">
                <div class="muted" style="font-size: 8px; margin-top: 2px;">{{ $verificationCode }}</div>
            </td>
            <td style="padding-left: 12px; vertical-align: top; font-size: 9px;">
                <div style="color: #d9534f; font-weight: bold; margin-bottom: 2px;">Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan oleh Balai Besar Sertifikasi Elektronik (BSrE) Badan Siber dan Sandi Negara.</div>
                <div>Dokumen digital yang asli dapat diperoleh dengan memindai QR Code, memasukkan kode pada Aplikasi NDE Pemerintah Daerah Provinsi Jawa Barat, atau mengakses tautan berikut:</div>
                <div style="margin-top: 2px;"><a href="{{ $verificationUrl }}" style="color: #0066cc; text-decoration: underline;">{{ $verificationUrl }}</a></div>
            </td>
        </tr>
    </table>
</body>
</html>