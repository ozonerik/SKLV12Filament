<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.4; }
        .center { text-align: center; }
        .mt-24 { margin-top: 24px; }
        .mt-16 { margin-top: 16px; }
        .title { font-size: 16px; font-weight: bold; }
        .subtitle { font-size: 13px; }
        .school-header { width: 100%; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 12px; }
        .school-logo { width: 70px; height: 70px; object-fit: contain; }
        .school-title { font-size: 15px; font-weight: bold; text-transform: uppercase; }
        .school-meta { font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 4px 0; }
        .label { width: 180px; }
        .tbl { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .tbl th, .tbl td { border: 1px solid #000; padding: 6px; font-size: 11px; }
        .tbl th { background: #f2f2f2; }
        
        /* CSS untuk area tanda tangan */
        .signature-container {
            float: right;
            width: 250px;
            text-align: center;
            margin-top: 30px;
        }
        .signature-image {
            height: 80px; /* Sesuaikan tinggi ttd */
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: 16px;
            z-index: 1;
        }
        .stamp-image {
            width: 86px;
            height: 86px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: 8px;
            z-index: 2;
            opacity: 0.9;
            object-fit: contain;
        }
        .signature-wrapper {
            position: relative;
            height: 104px;
            margin: 4px 0;
        }
        .spacer {
            height: 80px;
        }

        .verification-container {
            width: 260px;
            margin-top: 30px;
            font-size: 10px;
        }

        .verification-qr {
            width: 90px;
            height: 90px;
            border: 1px solid #000;
            padding: 4px;
        }

        .small-muted {
            font-size: 9px;
            color: #333;
        }
    </style>
</head>
<body>
    <table class="school-header">
        <tr>
            <td style="width: 80px; text-align: left; vertical-align: middle;">
                @if (! empty($school?->province_logo))
                    <img src="{{ public_path('storage/' . $school->province_logo) }}" alt="Logo Provinsi" class="school-logo">
                @endif
            </td>
            <td class="center" style="vertical-align: middle;">
                <div class="school-title">{{ $school?->name ?? 'SMK NEGERI' }}</div>
                <div class="school-meta">{{ $school?->address ?? '-' }}</div>
                <div class="school-meta">
                    Kodepos {{ $school?->postal_code ?? '-' }}
                    @if (! empty($school?->phone))
                        | Telp: {{ $school->phone }}
                    @endif
                    @if (! empty($school?->email))
                        | Email: {{ $school->email }}
                    @endif
                    @if (! empty($school?->website))
                        | Website: {{ $school->website }}
                    @endif
                </div>
            </td>
            <td style="width: 80px;"></td>
        </tr>
    </table>

    <div class="center">
        <div class="title">SURAT KETERANGAN LULUS (SKL)</div>
        <div class="subtitle">Nomor: {{ $skl->letter_number }}</div>
    </div>

    <div class="mt-24">
        <p>Yang bertanda tangan di bawah ini:</p>
        <table>
            <tr>
                <td class="label">Nama</td>
                <td>: {{ $headmaster?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NIP</td>
                <td>: {{ $headmaster?->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Pangkat/Golongan</td>
                <td>: {{ $headmaster?->rank ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="mt-16">
        <p>Menerangkan bahwa:</p>
        <table>
            <tr>
                <td class="label">Nama</td>
                <td>: {{ $student?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NIS / NISN</td>
                <td>: {{ $student?->nis ?? '-' }} / {{ $student?->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tempat/Tgl Lahir</td>
                <td>: {{ $student?->pob ?? '-' }}, {{ $student?->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jurusan</td>
                <td>: {{ $major?->konsentrasi_keahlian ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tahun Pelajaran</td>
                <td>: {{ $schoolYear?->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="mt-16">
        <p class="center" style="font-size: 14px; font-weight: bold; border: 1px solid #000; padding: 10px;">
            DINYATAKAN: {{ strtoupper($skl->status) }}
        </p>
    </div>

    <div class="mt-16">
        <p style="margin-bottom: 4px; font-weight: bold;">Transkrip Nilai</p>
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 80px;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($grades ?? []) as $i => $grade)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ $grade->subject?->name ?? '-' }}</td>
                        <td class="center">{{ $grade->score }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="center">Belum ada data nilai.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" style="text-align: right;">Rata-rata</th>
                    <th class="center">{{ number_format((float) ($averageScore ?? 0), 2, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="verification-container">
        <div style="font-weight: bold; margin-bottom: 6px;">Verifikasi SKL</div>
        <img src="{{ $qrCodeDataUri }}" alt="QR Verifikasi SKL" class="verification-qr">
        <div style="margin-top: 6px;">Kode: {{ $verificationCode }}</div>
        <div class="small-muted">
            Cek validitas:
            <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
        </div>
    </div>

    {{-- Bagian Tanda Tangan --}}
    <div class="signature-container">
        <div>{{ $skl->letter_date ? \Carbon\Carbon::parse($skl->letter_date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</div>
        <div>Kepala Sekolah,</div>
        
        <div class="signature-wrapper">
            @if($headmaster && $headmaster->ttd)
                {{-- Menggunakan public_path agar DomPDF bisa akses file lokal --}}
                <img src="{{ public_path('storage/' . $headmaster->ttd) }}" class="signature-image">
            @else
                <div class="spacer"></div>
            @endif

            @if (! empty($school?->school_stamp))
                <img src="{{ public_path('storage/' . $school->school_stamp) }}" alt="Stamp Sekolah" class="stamp-image">
            @endif
        </div>

        <div style="font-weight: bold; text-decoration: underline;">
            {{ $headmaster?->name ?? '...........................................' }}
        </div>
        <div>NIP. {{ $headmaster?->nip ?? '-' }}</div>
    </div>
</body>
</html>