<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Import Lulusan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100">
    <main class="mx-auto max-w-5xl px-4 py-10">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Preview Import Lulusan</h1>
                    <p class="mt-1 text-sm text-slate-600">Periksa ringkasan data sebelum menyimpan ke database.</p>
                </div>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Sesi preview berlaku 30 menit</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Baris Diproses</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format((int) ($preview['rows_processed'] ?? 0)) }}</p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-sm text-emerald-700">Student Dibuat</p>
                    <p class="mt-2 text-2xl font-semibold text-emerald-800">{{ number_format((int) ($preview['students_created'] ?? 0)) }}</p>
                </div>
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm text-blue-700">Student Diperbarui</p>
                    <p class="mt-2 text-2xl font-semibold text-blue-800">{{ number_format((int) ($preview['students_updated'] ?? 0)) }}</p>
                </div>
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                    <p class="text-sm text-indigo-700">SKL Dibuat</p>
                    <p class="mt-2 text-2xl font-semibold text-indigo-800">{{ number_format((int) ($preview['skls_created'] ?? 0)) }}</p>
                </div>
                <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-4">
                    <p class="text-sm text-cyan-700">SKL Diperbarui</p>
                    <p class="mt-2 text-2xl font-semibold text-cyan-800">{{ number_format((int) ($preview['skls_updated'] ?? 0)) }}</p>
                </div>
                <div class="rounded-xl border border-violet-200 bg-violet-50 p-4">
                    <p class="text-sm text-violet-700">Grade Dibuat</p>
                    <p class="mt-2 text-2xl font-semibold text-violet-800">{{ number_format((int) ($preview['grades_created'] ?? 0)) }}</p>
                </div>
                <div class="rounded-xl border border-fuchsia-200 bg-fuchsia-50 p-4 sm:col-span-2 lg:col-span-3">
                    <p class="text-sm text-fuchsia-700">Grade Diperbarui</p>
                    <p class="mt-2 text-2xl font-semibold text-fuchsia-800">{{ number_format((int) ($preview['grades_updated'] ?? 0)) }}</p>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ $confirmUrl }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    Simpan ke Database
                </a>
                <a href="{{ $cancelUrl }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    Batalkan Import
                </a>
            </div>
        </div>
    </main>
</body>
</html>
