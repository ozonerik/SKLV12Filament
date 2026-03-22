<?php

namespace App\Http\Controllers;

use App\Services\LulusanExcelImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Throwable;

class LulusanImportController extends Controller
{
    public function confirm(string $token, LulusanExcelImportService $service): RedirectResponse
    {
        $key = "lulusan_import_preview:{$token}";
        $payload = Cache::get($key);

        if (! is_array($payload) || empty($payload['stored_path'])) {
            return redirect('/')
                ->with('lulusan_import_feedback', [
                    'type' => 'danger',
                    'message' => 'Sesi preview import tidak ditemukan atau sudah kedaluwarsa.',
                ]);
        }

        $storedPath = (string) $payload['stored_path'];
        $returnUrl = (string) ($payload['return_url'] ?? '/');

        try {
            $result = $service->import(Storage::disk('local')->path($storedPath));

            $message =
                "Import berhasil. Baris diproses: {$result['rows_processed']} | " .
                "Student dibuat: {$result['students_created']} | Student diperbarui: {$result['students_updated']} | " .
                "SKL dibuat: {$result['skls_created']} | SKL diperbarui: {$result['skls_updated']} | " .
                "Grade dibuat: {$result['grades_created']} | Grade diperbarui: {$result['grades_updated']}";

            $type = 'success';
        } catch (Throwable $exception) {
            report($exception);
            $message = 'Import gagal: ' . $exception->getMessage();
            $type = 'danger';
        } finally {
            Storage::disk('local')->delete($storedPath);
            Cache::forget($key);
        }

        return redirect($returnUrl)->with('lulusan_import_feedback', [
            'type' => $type,
            'message' => $message,
        ]);
    }

    public function cancel(string $token): RedirectResponse
    {
        $key = "lulusan_import_preview:{$token}";
        $payload = Cache::get($key);

        if (is_array($payload) && ! empty($payload['stored_path'])) {
            Storage::disk('local')->delete((string) $payload['stored_path']);
        }

        $returnUrl = is_array($payload) ? (string) ($payload['return_url'] ?? '/') : '/';
        Cache::forget($key);

        return redirect($returnUrl)->with('lulusan_import_feedback', [
            'type' => 'warning',
            'message' => 'Import dibatalkan. Tidak ada data yang disimpan ke database.',
        ]);
    }
}
