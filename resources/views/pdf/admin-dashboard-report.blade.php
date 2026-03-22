<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { margin: 0; font-size: 18px; }
        h2 { margin: 18px 0 8px; font-size: 14px; }
        .meta { margin-top: 6px; color: #444; }
        .card { border: 1px solid #ddd; border-radius: 6px; padding: 10px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f4f4f4; }
        .small { font-size: 10px; color: #555; }
    </style>
</head>
<body>
    <h1>Laporan Dashboard Admin</h1>
    <div class="meta">Tahun Pelajaran: <strong>{{ $schoolYearName }}</strong></div>
    <div class="meta">Digenerate: {{ $generatedAt->format('d/m/Y H:i') }}</div>

    <h2>1. Grafik Status Kelulusan</h2>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Jumlah Siswa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($graduationData as $label => $value)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h2>2. Grafik Unduhan SKL</h2>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Jumlah Siswa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($downloadData as $label => $value)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h2>3. Grafik Hasil Kuesioner (Distribusi Per Pertanyaan)</h2>
    <div class="small">Setiap pertanyaan menampilkan jumlah jawaban untuk tiap opsi.</div>

    @forelse ($questionDistributions as $question)
        <div class="card">
            <div><strong>{{ $question['question_label'] }}</strong> - {{ $question['question_text'] }}</div>
            <table>
                <thead>
                    <tr>
                        <th>Opsi</th>
                        <th>Teks Opsi</th>
                        <th>Jumlah Jawaban</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($question['options'] as $option)
                        <tr>
                            <td>{{ $option['option_label'] }}</td>
                            <td>{{ $option['option_text'] }}</td>
                            <td>{{ $option['total'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="card">Belum ada data pertanyaan pilihan ganda untuk tahun pelajaran ini.</div>
    @endforelse
</body>
</html>
