<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Kelulusan - SMKN 1 Krangkeng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 52%, #38bdf8 100%);
        }

        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 30px rgba(30, 58, 138, 0.12);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">

    <nav class="fixed w-full z-40 transition-all duration-300 glass-effect shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-900 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h1 class="text-blue-900 font-bold text-lg leading-tight">SMKN 1 KRANGKENG</h1>
                        <p class="text-xs text-gray-500 font-medium">Sistem Informasi Kelulusan</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-4 lg:space-x-6">
                    <a href="#beranda" class="text-gray-700 hover:text-blue-600 font-medium">Beranda</a>
                    <a href="#prosedur" class="text-gray-700 hover:text-blue-600 font-medium">Prosedur</a>
                    <a href="/validasi-skl" class="bg-white border-2 border-blue-600 text-blue-600 px-4 py-2 rounded-full font-semibold hover:bg-blue-50 transition">
                        <i class="fas fa-check-circle mr-2"></i>Cek Validitas SKL
                    </a>
                    <a href="/admin/login" class="bg-slate-700 text-white px-4 py-2 rounded-full font-semibold hover:bg-slate-800 shadow-md transition">
                        Login Admin
                    </a>
                    <a href="/siswa/login" class="bg-blue-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-blue-700 shadow-md transition">
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
                <a href="#beranda" class="block px-3 py-2 text-gray-700 font-medium">Beranda</a>
                <a href="#prosedur" class="block px-3 py-2 text-gray-700 font-medium">Prosedur</a>
                <hr>
                <a href="/validasi-skl" class="block px-3 py-2 text-blue-600 font-bold">Cek Validitas SKL</a>
                <a href="/admin/login" class="block px-3 py-2 text-slate-700 font-bold">Login Admin</a>
                <a href="/siswa/login" class="block px-3 py-2 text-blue-700 font-bold">Login Siswa</a>
            </div>
        </div>
    </nav>

    <header id="beranda" class="pt-32 pb-16 hero-gradient text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center md:text-left flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 space-y-6">
                <span class="inline-block px-4 py-1 bg-blue-500/30 rounded-full text-sm font-semibold tracking-wider">TAHUN PELAJARAN BERJALAN</span>
                <h2 class="text-4xl md:text-6xl font-extrabold leading-tight">
                    Selamat Datang di <br><span class="text-yellow-300">Portal Kelulusan</span>
                </h2>
                <p class="text-lg text-blue-100 max-w-lg">
                    Silakan login untuk mengecek status kelulusan dan mengunduh Surat Keterangan Lulus (SKL) digital.
                    Untuk pihak umum, gunakan menu cek validitas SKL.
                </p>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 pt-4">
                    <a href="/siswa/login" class="inline-flex items-center justify-center bg-yellow-400 text-blue-900 px-8 py-3 rounded-lg font-bold text-lg hover:bg-yellow-300 transition shadow-lg">
                        Login Siswa
                    </a>
                    <a href="/validasi-skl" class="inline-flex items-center justify-center bg-white/10 backdrop-blur-md border border-white/20 px-8 py-3 rounded-lg font-bold text-lg hover:bg-white/20 transition">
                        Cek Validitas SKL
                    </a>
                </div>
            </div>
            <div class="md:w-1/2 mt-12 md:mt-0 flex justify-center">
                <div class="relative">
                    <i class="fas fa-user-graduate text-[15rem] text-blue-200/20"></i>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-64 h-64 bg-white/10 rounded-full animate-pulse flex items-center justify-center">
                            <i class="fas fa-scroll text-8xl text-yellow-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="prosedur" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-bold text-blue-900">Langkah Penggunaan</h3>
                <p class="text-gray-500 mt-2">Ikuti prosedur di bawah ini untuk mendapatkan SKL digital</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 card-hover">
                    <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-6 text-2xl">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Login Siswa</h4>
                    <p class="text-gray-600">Masuk melalui menu Login Siswa untuk melihat status kelulusan dan akses dokumen SKL.</p>
                </div>

                <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 card-hover">
                    <div class="w-14 h-14 bg-slate-100 text-slate-700 rounded-xl flex items-center justify-center mb-6 text-2xl">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Login Admin</h4>
                    <p class="text-gray-600">Admin sekolah dapat mengelola data siswa, nilai, serta publikasi dan penerbitan SKL.</p>
                </div>

                <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 card-hover">
                    <div class="w-14 h-14 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-6 text-2xl">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Cek Validitas SKL</h4>
                    <p class="text-gray-600">Pihak ketiga dapat memverifikasi keaslian SKL menggunakan kode unik atau tautan QR publik.</p>
                </div>
            </div>

            <div class="mt-12 p-8 rounded-2xl bg-blue-50 border border-blue-100">
                <h4 class="text-xl font-bold text-blue-900 mb-3">Verifikasi Cepat</h4>
                <p class="text-blue-900/80 mb-4">Masukkan kode unik untuk langsung cek validitas SKL.</p>
                <form class="grid sm:grid-cols-[1fr_auto] gap-3" method="GET" action="{{ route('skl.verify.search') }}">
                    <input
                        type="text"
                        name="code"
                        required
                        placeholder="Contoh: A1B2C3D4E5F6"
                        class="w-full px-4 py-3 rounded-lg border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                    >
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                        Cek Sekarang
                    </button>
                </form>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-400 py-12">
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

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
