<?php

namespace App\Http\Controllers;

use App\Models\Skl;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicSklVerificationController extends Controller
{
    public function landing(): View
    {
        return view('public.landing');
    }

    public function home(Request $request): View
    {
        $code = strtoupper(trim((string) $request->query('code', '')));

        return view('public.skl-validity', $this->buildViewData($code));
    }

    public function show(string $code): View
    {
        $normalizedCode = strtoupper(trim($code));

        return view('public.skl-validity', $this->buildViewData($normalizedCode));
    }

    protected function buildViewData(string $code): array
    {
        if ($code === '') {
            return [
                'code' => '',
                'checked' => false,
                'isValid' => null,
                'message' => null,
                'skl' => null,
            ];
        }

        $skl = Skl::query()
            ->with(['student.major', 'student.schoolYear'])
            ->where('verification_code', $code)
            ->first();

        if (! $skl) {
            return [
                'code' => $code,
                'checked' => true,
                'isValid' => false,
                'message' => 'Kode verifikasi tidak ditemukan.',
                'skl' => null,
            ];
        }

        $isValid = $skl->isPublished();

        return [
            'code' => $code,
            'checked' => true,
            'isValid' => $isValid,
            'message' => $isValid
                ? 'SKL valid dan terdaftar.'
                : 'SKL ditemukan, tetapi belum dipublikasikan.',
            'skl' => $skl,
        ];
    }
}
