<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
            color: #111; 
            line-height: 1.4; 
            margin: 0;
            padding: 0;
        }
        h1 { margin: 0; font-size: 18px; font-weight: 700; }
        h2 { margin: 16px 0 10px; font-size: 14px; font-weight: 700; border-bottom: 2px solid #e5e7eb; padding-bottom: 6px; }
        .meta { margin-top: 6px; color: #666; font-size: 10px; }
        .card { border: 1px solid #e5e7eb; border-radius: 4px; padding: 12px; margin-top: 10px; page-break-inside: avoid; }
        
        /* Chart Styles */
        .chart-visual { margin: 10px 0; }
        .chart-row { display: flex; margin-bottom: 8px; align-items: center; page-break-inside: avoid; }
        .chart-label { width: 100px; font-weight: 600; color: #374151; font-size: 10px; }
        .chart-bar { flex: 1; background: #e5e7eb; height: 24px; margin: 0 8px; border-radius: 3px; position: relative; overflow: hidden; }
        .chart-bar-fill { height: 100%; display: flex; align-items: center; justify-content: flex-end; padding-right: 6px; color: white; font-size: 9px; font-weight: 600; }
        .chart-value { width: 60px; text-align: right; color: #111; font-weight: 600; font-size: 10px; }
        
        .bar-green { background: #10B981; }
        .bar-red { background: #EF4444; }
        .bar-blue { background: #3B82F6; }
        .bar-amber { background: #F59E0B; }
        .bar-indigo { background: #6366F1; }
        .bar-purple { background: #A855F7; }
        .bar-pink { background: #EC4899; }
        .bar-cyan { background: #06B6D4; }
        
        .table-chart {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 10px;
        }
        .table-chart th, .table-chart td {
            border: 1px solid #e5e7eb;
            padding: 6px;
            text-align: left;
        }
        .table-chart th {
            background: #f9fafb;
            font-weight: 700;
            color: #374151;
        }
        .table-chart td {
            color: #111;
        }
        .table-chart tr:nth-child(even) {
            background: #fafafa;
        }
        
        .question-section { page-break-inside: avoid; }
        .small { font-size: 9px; color: #666; margin-bottom: 8px; }
    </style>
</head>
<body>
    <h1>Laporan Dashboard Admin</h1>
    <div class="meta">Tahun Pelajaran: <strong>{{ $schoolYearName }}</strong></div>
    <div class="meta">Digenerate: {{ $generatedAt->format('d/m/Y H:i') }}</div>

    <h2>1. Distribusi Kelulusan</h2>
    <div class="card">
        <div class="chart-visual">
            @php
                $totalGrad = array_sum($graduationData);
                $colors = ['#10B981', '#EF4444'];
                $index = 0;
            @endphp
            
            @foreach ($graduationData as $label => $value)
                @php
                    $percentage = $totalGrad > 0 ? round(($value / $totalGrad) * 100) : 0;
                    $color = $colors[$index] ?? '#9CA3AF';
                    $index++;
                @endphp
                <div class="chart-row">
                    <div class="chart-label">{{ $label }}</div>
                    <div class="chart-bar">
                        <div class="chart-bar-fill" style="width: {{ $percentage }}%; background: {{ $color }};">
                            {{ $percentage }}%
                        </div>
                    </div>
                    <div class="chart-value">{{ $value }} siswa</div>
                </div>
            @endforeach
        </div>
        
        <table class="table-chart">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Jumlah Siswa</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($graduationData as $label => $value)
                    @php
                        $percentage = $totalGrad > 0 ? round(($value / $totalGrad) * 100, 2) : 0;
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $value }}</td>
                        <td>{{ $percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h2>2. Unduhan SKL</h2>
    <div class="card">
        <div class="chart-visual">
            @php
                $totalDl = array_sum($downloadData);
                $dlColors = ['#3B82F6', '#F59E0B'];
                $dlIndex = 0;
            @endphp
            
            @foreach ($downloadData as $label => $value)
                @php
                    $percentage = $totalDl > 0 ? round(($value / $totalDl) * 100) : 0;
                    $color = $dlColors[$dlIndex] ?? '#9CA3AF';
                    $dlIndex++;
                @endphp
                <div class="chart-row">
                    <div class="chart-label">{{ $label }}</div>
                    <div class="chart-bar">
                        <div class="chart-bar-fill" style="width: {{ $percentage }}%; background: {{ $color }};">
                            {{ $percentage }}%
                        </div>
                    </div>
                    <div class="chart-value">{{ $value }} siswa</div>
                </div>
            @endforeach
        </div>
        
        <table class="table-chart">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Jumlah Siswa</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($downloadData as $label => $value)
                    @php
                        $percentage = $totalDl > 0 ? round(($value / $totalDl) * 100, 2) : 0;
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $value }}</td>
                        <td>{{ $percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h2>3. Hasil Kuesioner (Distribusi Per Pertanyaan)</h2>
    <div class="small">Setiap pertanyaan menampilkan jumlah jawaban untuk tiap opsi.</div>

    @forelse ($questionDistributions as $question)
        <div class="card question-section">
            <div><strong>{{ $question['question_label'] }}</strong></div>
            <div style="font-size: 9px; color: #666; margin: 4px 0 8px;">{{ $question['question_text'] }}</div>
            
            <div class="chart-visual">
                @php
                    $totalOpts = collect($question['options'])->sum(fn($o) => $o['total']);
                    $optColors = ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#7c3aed', '#0ea5e9', '#ec4899', '#14b8a6', '#84cc16', '#f97316'];
                    $optIndex = 0;
                @endphp
                
                @foreach ($question['options'] as $option)
                    @php
                        $percentage = $totalOpts > 0 ? round(($option['total'] / $totalOpts) * 100) : 0;
                        $color = $optColors[$optIndex % count($optColors)] ?? '#9CA3AF';
                        $optIndex++;
                    @endphp
                    <div class="chart-row">
                        <div class="chart-label">{{ $option['option_label'] }}</div>
                        <div class="chart-bar">
                            <div class="chart-bar-fill" style="width: {{ $percentage }}%; background: {{ $color }};">
                                {{ $percentage }}%
                            </div>
                        </div>
                        <div class="chart-value">{{ $option['total'] }}</div>
                    </div>
                @endforeach
            </div>
            
            <table class="table-chart">
                <thead>
                    <tr>
                        <th>Opsi</th>
                        <th>Teks Opsi</th>
                        <th>Jumlah Jawaban</th>
                        <th>Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($question['options'] as $option)
                        @php
                            $percentage = $totalOpts > 0 ? round(($option['total'] / $totalOpts) * 100, 2) : 0;
                        @endphp
                        <tr>
                            <td>{{ $option['option_label'] }}</td>
                            <td>{{ $option['option_text'] }}</td>
                            <td>{{ $option['total'] }}</td>
                            <td>{{ $percentage }}%</td>
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
