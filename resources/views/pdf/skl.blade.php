<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .center { text-align: center; }
        .mt-24 { margin-top: 24px; }
        .mt-16 { margin-top: 16px; }
        .title { font-size: 16px; font-weight: bold; }
        .subtitle { font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 4px 0; }
        .label { width: 180px; }
    </style>
</head>
<body>
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
                <td class="label">NIS</td>
                <td>: {{ $student?->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NISN</td>
                <td>: {{ $student?->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tempat/Tgl Lahir</td>
                <td>: {{ $student?->pob ?? '-' }}, {{ optional($student?->dob)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">Jurusan</td>
                <td>: {{ $major?->program_keahlian ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tahun Pelajaran</td>
                <td>: {{ $schoolYear?->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="mt-16">
        <p class="center" style="font-size: 14px; font-weight: bold;">
            DINYATAKAN: {{ strtoupper($skl->status) }}
        </p>
    </div>

    <div class="mt-24" style="width: 100%;">
        <div style="width: 50%; float: right; text-align: center;">
            <div>{{ optional($skl->letter_date)->format('d/m/Y') }}</div>
            <div class="mt-16">Kepala Sekolah,</div>
            <div style="height: 72px;"></div>
            <div style="font-weight: bold; text-decoration: underline;">
                {{ $headmaster?->name ?? '-' }}
            </div>
            <div>NIP. {{ $headmaster?->nip ?? '-' }}</div>
        </div>
    </div>
</body>
</html>

