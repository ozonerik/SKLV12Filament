<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\School;
use App\Models\Skl;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class PublicSklVerificationController extends Controller
{
    public function landing(): View
    {
        return view('public.landing', [
            'school' => School::query()->first(),
        ]);
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

    public function download(string $code): StreamedResponse
    {
        $normalizedCode = strtoupper(trim($code));

        $skl = Skl::query()
            ->with(['student.major', 'student.schoolYear.headmaster'])
            ->where('verification_code', $normalizedCode)
            ->firstOrFail();

        abort_unless($skl->isPublished(), 403);

        $student = $skl->student;
        $schoolYear = $student?->schoolYear;
        $headmaster = $schoolYear?->headmaster;
        $major = $student?->major;

        $grades = Grade::query()
            ->where('student_id', $skl->student_id)
            ->with('subject')
            ->get();

        $categoryOrder = [
            'Umum' => 1,
            'Kejuruan' => 2,
            'Pilihan' => 3,
            'Mulok' => 4,
        ];

        $grades = $grades
            ->sortBy(function (Grade $grade) use ($categoryOrder): array {
                $category = (string) ($grade->subject?->category ?? '');

                return [
                    $categoryOrder[$category] ?? 99,
                    (string) ($grade->subject?->kode ?? ''),
                    (string) ($grade->subject?->name ?? ''),
                ];
            })
            ->values();

        $groupedGrades = collect([
            'Umum' => $grades->filter(fn (Grade $grade) => ($grade->subject?->category ?? null) === 'Umum')->values(),
            'Kejuruan' => $grades->filter(fn (Grade $grade) => ($grade->subject?->category ?? null) === 'Kejuruan')->values(),
            'Pilihan' => $grades->filter(fn (Grade $grade) => ($grade->subject?->category ?? null) === 'Pilihan')->values(),
            'Mulok' => $grades->filter(fn (Grade $grade) => ($grade->subject?->category ?? null) === 'Mulok')->values(),
        ]);

        $average = (float) ($grades->avg('score') ?? 0);
        $verificationCode = $skl->ensureVerificationCode();
        $verificationUrl = route('skl.verify.show', ['code' => $verificationCode]);
        $school = School::query()->first();

        $qrCodeDataUri = (new QRCode(new QROptions([
            'outputType' => QROutputInterface::GDIMAGE_PNG,
            'eccLevel' => QRCode::ECC_M,
            'scale' => 5,
            'outputBase64' => true,
        ])))->render($verificationUrl);

        $pdf = Pdf::loadView('pdf.skl', [
            'skl' => $skl,
            'student' => $student,
            'schoolYear' => $schoolYear,
            'major' => $major,
            'headmaster' => $headmaster,
            'grades' => $grades,
            'groupedGrades' => $groupedGrades,
            'averageScore' => $average,
            'verificationCode' => $verificationCode,
            'verificationUrl' => $verificationUrl,
            'qrCodeDataUri' => $qrCodeDataUri,
            'school' => $school,
        ])->setPaper([0, 0, 595.28, 935.43], 'portrait');

        $skl->forceFill([
            'downloaded_at' => now(),
        ])->save();

        $filenameNisn = $student?->nisn ?: $verificationCode;

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "SKL-{$filenameNisn}.pdf",
            ['Content-Type' => 'application/pdf']
        );
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
            ->with(['student.major', 'student.schoolYear', 'student.grades'])
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
