<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Validitas SKL</title>
    <style>
        :root {
            --bg: #f3efe8;
            --card: #ffffff;
            --ink: #1f2937;
            --muted: #6b7280;
            --accent: #0f766e;
            --danger: #b91c1c;
            --ok: #166534;
            --line: #e5e7eb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 10% 10%, #fef3c7 0, transparent 28%),
                radial-gradient(circle at 85% 15%, #dbeafe 0, transparent 25%),
                var(--bg);
            min-height: 100vh;
        }

        .wrap {
            max-width: 860px;
            margin: 0 auto;
            padding: 28px 16px 48px;
        }

        .panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 18px 40px rgba(17, 24, 39, 0.08);
            padding: 22px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 30px;
            line-height: 1.2;
        }

        .sub {
            margin: 0 0 20px;
            color: var(--muted);
        }

        .row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
        }

        input[type="text"] {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 16px;
            text-transform: uppercase;
        }

        button {
            border: 0;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            padding: 12px 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .status {
            margin-top: 18px;
            border-radius: 12px;
            padding: 14px;
            border: 1px solid;
            font-weight: 600;
        }

        .status.ok {
            color: var(--ok);
            border-color: #bbf7d0;
            background: #f0fdf4;
        }

        .status.bad {
            color: var(--danger);
            border-color: #fecaca;
            background: #fef2f2;
        }

        .detail {
            margin-top: 16px;
            border-top: 1px dashed #d1d5db;
            padding-top: 16px;
            display: grid;
            gap: 8px;
        }

        .pair {
            display: grid;
            grid-template-columns: 170px 1fr;
            gap: 10px;
            font-size: 15px;
        }

        .pair .label { color: var(--muted); }

        .link {
            margin-top: 12px;
            color: var(--accent);
            word-break: break-all;
        }

        @media (max-width: 700px) {
            .row { grid-template-columns: 1fr; }
            .pair { grid-template-columns: 1fr; gap: 2px; }
            h1 { font-size: 25px; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="panel">
            <h1>Cek Validitas SKL</h1>
            <p class="sub">Masukkan kode unik verifikasi SKL untuk memastikan dokumen terdaftar dan valid.</p>

            <form method="GET" action="{{ route('skl.verify.search') }}">
                <div class="row">
                    <input
                        type="text"
                        name="code"
                        value="{{ $code }}"
                        placeholder="Contoh: A1B2C3D4E5F6"
                        required
                    >
                    <button type="submit">Cek Validitas</button>
                </div>
            </form>

            @if ($checked)
                <div class="status {{ $isValid ? 'ok' : 'bad' }}">
                    {{ $message }}
                </div>
            @endif

            @if ($skl)
                <div class="detail">
                    <div class="pair">
                        <div class="label">Kode Verifikasi</div>
                        <div>{{ $skl->verification_code }}</div>
                    </div>
                    <div class="pair">
                        <div class="label">Nomor SKL</div>
                        <div>{{ $skl->letter_number }}</div>
                    </div>
                    <div class="pair">
                        <div class="label">Nama Siswa</div>
                        <div>{{ $skl->student?->name ?? '-' }}</div>
                    </div>
                    <div class="pair">
                        <div class="label">NISN</div>
                        <div>{{ $skl->student?->nisn ?? '-' }}</div>
                    </div>
                    <div class="pair">
                        <div class="label">Jurusan</div>
                        <div>{{ $skl->student?->major?->konsentrasi_keahlian ?? '-' }}</div>
                    </div>
                    <div class="pair">
                        <div class="label">Tahun Pelajaran</div>
                        <div>{{ $skl->student?->schoolYear?->name ?? '-' }}</div>
                    </div>
                    <div class="pair">
                        <div class="label">Status Kelulusan</div>
                        <div>{{ $skl->status }}</div>
                    </div>
                    <div class="pair">
                        <div class="label">Waktu Publikasi</div>
                        <div>{{ $skl->published_at?->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>
                    <div class="link">
                        Link validasi publik: {{ route('skl.verify.show', ['code' => $skl->verification_code]) }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
