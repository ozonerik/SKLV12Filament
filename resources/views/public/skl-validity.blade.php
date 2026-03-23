<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Validitas SKL - SMKN 1 Krangkeng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 52%, #38bdf8 100%);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="fixed w-full z-40 transition-all duration-300 glass-effect shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-900 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <p class="text-blue-900 font-bold text-lg leading-tight">SMKN 1 KRANGKENG</p>
                        <p class="text-xs text-gray-500 font-medium">Sistem Informasi Kelulusan</p>
                    </div>
                </a>

                <div class="hidden md:flex items-center space-x-4 lg:space-x-6">
                    <a href="/" class="text-gray-700 hover:text-blue-600 font-medium">Beranda</a>
                    <a href="/#prosedur" class="text-gray-700 hover:text-blue-600 font-medium">Prosedur</a>
                    <a href="/validasi-skl" class="bg-blue-600 border-2 border-blue-600 text-white px-4 py-2 rounded-full font-semibold shadow-md transition">
                        <i class="fas fa-check-circle mr-2"></i>Cek Validitas SKL
                    </a>
                    <a href="/admin/login" class="bg-slate-700 text-white px-4 py-2 rounded-full font-semibold hover:bg-slate-800 shadow-md transition">
                        Login Admin
                    </a>
                    <a href="/siswa/login" class="bg-white border-2 border-blue-600 text-blue-600 px-5 py-2 rounded-full font-semibold hover:bg-blue-50 shadow-md transition">
                        Login Siswa <i class="fas fa-sign-in-alt ml-2"></i>
                    </a>
                </div>

                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-blue-900 focus:outline-none" aria-label="Buka menu">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden glass-effect border-t">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="/" class="block px-3 py-2 text-gray-700 font-medium">Beranda</a>
                <a href="/#prosedur" class="block px-3 py-2 text-gray-700 font-medium">Prosedur</a>
                <hr>
                <a href="/validasi-skl" class="block px-3 py-2 text-blue-600 font-bold">Cek Validitas SKL</a>
                <a href="/admin/login" class="block px-3 py-2 text-slate-700 font-bold">Login Admin</a>
                <a href="/siswa/login" class="block px-3 py-2 text-blue-700 font-bold">Login Siswa</a>
            </div>
        </div>
    </nav>

    {{-- Hero strip --}}
    <div class="hero-gradient pt-32 pb-10 text-white text-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                <i class="fas fa-qrcode text-3xl"></i>
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold">Cek Validitas SKL</h2>
            <p class="text-blue-100 mt-2 text-lg">Verifikasi keaslian Surat Keterangan Lulus menggunakan kode unik.</p>
        </div>
    </div>

    {{-- Main content --}}
    <main class="flex-1 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Search card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-blue-900 mb-1">Masukkan Kode Verifikasi</h3>
                <p class="text-gray-500 text-sm mb-5">Kode terdapat pada dokumen SKL, biasanya di bawah QR Code.</p>

                <form method="GET" action="{{ route('skl.verify.search') }}" class="grid sm:grid-cols-[1fr_auto] gap-3">
                    <input
                        type="text"
                        name="code"
                        value="{{ $code }}"
                        placeholder="Contoh: A1B2C3D4E5F6"
                        required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase text-base"
                    >
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition whitespace-nowrap">
                        <i class="fas fa-search mr-2"></i>Cek Sekarang
                    </button>
                </form>
            </div>

            {{-- Result --}}
            @if ($checked)
                @if ($isValid)
                    <div class="flex items-start gap-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl p-6">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-lg">SKL Valid</p>
                            <p class="text-sm mt-0.5">{{ $message }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl p-6">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-lg">SKL Tidak Valid</p>
                            <p class="text-sm mt-0.5">{{ $message }}</p>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Detail card --}}
            @if ($skl)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-blue-50 border-b border-blue-100 px-8 py-4">
                        <h3 class="text-blue-900 font-bold text-lg flex items-center gap-2">
                            <i class="fas fa-id-card"></i> Detail Surat Keterangan Lulus
                        </h3>
                    </div>
                    <div class="px-8 py-6 divide-y divide-gray-100">
                        @php
                            $avgScore = $skl->student?->grades->avg('score');
                            $jenisKelamin = match ($skl->student?->jenis_kelamin) {
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                                default => '-',
                            };
                            $rows = [
                                ['label' => 'Kode Verifikasi', 'icon' => 'fa-key',          'value' => $skl->verification_code],
                                ['label' => 'Nomor SKL',       'icon' => 'fa-file-alt',      'value' => $skl->letter_number ?? '-'],
                                ['label' => 'Tanggal Surat',   'icon' => 'fa-calendar-day',  'value' => $skl->letter_date?->format('d/m/Y') ?? '-'],
                                ['label' => 'Nama Siswa',      'icon' => 'fa-user',          'value' => $skl->student?->name ?? '-'],
                                ['label' => 'NISN',            'icon' => 'fa-id-badge',      'value' => $skl->student?->nisn ?? '-'],
                                ['label' => 'NIS',             'icon' => 'fa-address-card',  'value' => $skl->student?->nis ?? '-'],
                                ['label' => 'Tempat Lahir',    'icon' => 'fa-map-marker-alt','value' => $skl->student?->pob ?? '-'],
                                ['label' => 'Tanggal Lahir',   'icon' => 'fa-calendar',      'value' => $skl->student?->dob ? \Carbon\Carbon::parse($skl->student->dob)->format('d/m/Y') : '-'],
                                ['label' => 'Jenis Kelamin',   'icon' => 'fa-venus-mars',    'value' => $jenisKelamin],
                                ['label' => 'Nama Ayah',       'icon' => 'fa-user-tie',      'value' => $skl->student?->father_name ?? '-'],
                                ['label' => 'Jurusan',         'icon' => 'fa-book',          'value' => $skl->student?->major?->konsentrasi_keahlian ?? '-'],
                                ['label' => 'Tahun Pelajaran', 'icon' => 'fa-calendar-alt',  'value' => $skl->student?->schoolYear?->name ?? '-'],
                                ['label' => 'Status Kelulusan','icon' => 'fa-graduation-cap','value' => $skl->status ?? '-'],
                                ['label' => 'Rata-rata Nilai', 'icon' => 'fa-star-half-alt', 'value' => $avgScore !== null ? number_format($avgScore, 2) : '-'],
                                ['label' => 'Waktu Publikasi', 'icon' => 'fa-clock',         'value' => $skl->published_at?->format('d/m/Y H:i') ?? '-'],
                            ];
                        @endphp

                        @foreach ($rows as $row)
                            <div class="flex items-start gap-4 py-3.5">
                                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas {{ $row['icon'] }} text-blue-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">{{ $row['label'] }}</p>
                                    <p class="text-gray-800 font-semibold mt-0.5">{{ $row['value'] }}</p>
                                </div>
                            </div>
                        @endforeach

                        <div class="pt-4">
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-2">Link Validasi Publik</p>
                            <a href="{{ route('skl.verify.show', ['code' => $skl->verification_code]) }}"
                               class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 text-sm font-medium break-all">
                                <i class="fas fa-link flex-shrink-0"></i>
                                {{ route('skl.verify.show', ['code' => $skl->verification_code]) }}
                            </a>

                            @if ($skl->isPublished())
                                <div class="mt-4">
                                    <a href="{{ route('skl.verify.download', ['code' => $skl->verification_code]) }}"
                                       class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                                        <i class="fas fa-download"></i>
                                        Download SKL (PDF)
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-400 py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center border-b border-gray-800 pb-8">
                <div class="mb-4 md:mb-0 text-center md:text-left">
                    <h4 class="text-white font-bold text-xl">SMKN 1 Krangkeng</h4>
                    <p class="text-sm">Mewujudkan insan yang berakhlak mulia dan kompeten.</p>
                </div>
                <div class="flex space-x-6 text-2xl">
                    <a href="#" class="hover:text-white" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="hover:text-white" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="hover:text-white" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="mt-8 text-center text-xs">
                &copy; {{ date('Y') }} SMKN 1 Krangkeng. All Rights Reserved.
            </div>
        </div>
    </footer>

    <script>
        const menuBtn = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
    </script>
</body>
</html>
