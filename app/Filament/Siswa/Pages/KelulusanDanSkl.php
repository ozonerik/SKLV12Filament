<?php

namespace App\Filament\Siswa\Pages;

use App\Models\Grade;
use App\Models\Skl;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Panel;

class KelulusanDanSkl extends Page
{
    protected static string $routePath = '/kelulusan';

    protected static ?string $title = 'Kelulusan & SKL';

    protected static bool $shouldRegisterNavigation = true;

    public function mount(): void
    {
        $student = Filament::auth()->user();

        abort_unless($student, 403);

        $skl = $this->getSkl();

        if (! $skl->is_questionnaire_completed) {
            $this->redirect(IsiKuesioner::getUrl(panel: 'siswa'));
        }
    }

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    public function content(Schema $schema): Schema
    {
        $skl = $this->getSkl();
        $average = $this->getAverageScore();

        return $schema->components([
            Section::make('Status Kelulusan')
                ->schema([
                    \Filament\Infolists\Components\TextEntry::make('letter_number')
                        ->label('Nomor Surat')
                        ->state($skl->letter_number),
                    \Filament\Infolists\Components\TextEntry::make('status')
                        ->label('Status')
                        ->state($skl->status)
                        ->badge()
                        ->color($skl->status === 'Lulus' ? 'success' : 'danger'),
                    \Filament\Infolists\Components\TextEntry::make('published_at')
                        ->label('Dibuka')
                        ->state($skl->published_at?->format('d/m/Y H:i')),
                    \Filament\Infolists\Components\TextEntry::make('avg_score')
                        ->label('Rata-rata Nilai')
                        ->state(number_format($average, 2, ',', '.')),
                    \Filament\Infolists\Components\TextEntry::make('message')
                        ->label('')
                        ->state($skl->isPublished()
                            ? 'Anda sudah bisa mengunduh SKL.'
                            : 'SKL belum dibuka. Silakan cek kembali sesuai jadwal.'),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        $skl = $this->getSkl();

        return [
            Action::make('download')
                ->label('Download SKL (PDF)')
                ->disabled((! $skl->is_questionnaire_completed) || (! $skl->isPublished()))
                ->action('downloadPdf'),
        ];
    }

    public function downloadPdf()
    {
        $skl = $this->getSkl();

        abort_unless($skl->is_questionnaire_completed, 403);
        abort_unless($skl->isPublished(), 403);

        // FIX: Ambil student beserta relasi major dan schoolYear-nya
        $student = $skl->student()
            ->with(['major', 'schoolYear.headmaster'])
            ->first();

        // Ambil schoolYear dari object student
        $schoolYear = $student?->schoolYear;
        $headmaster = $schoolYear?->headmaster; // Di sini kita mendapatkan objek Headmaster
        $major = $student?->major;
        $grades = Grade::query()
            ->where('student_id', $skl->student_id)
            ->with('subject')
            ->orderBy('subject_id')
            ->get();
        $average = (float) ($grades->avg('score') ?? 0);
        $verificationCode = $skl->ensureVerificationCode();
        $verificationUrl = route('skl.verify.show', ['code' => $verificationCode]);

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
            'headmaster' => $headmaster, // Data Headmaster (termasuk kolom 'ttd')
            'grades' => $grades,
            'averageScore' => $average,
            'verificationCode' => $verificationCode,
            'verificationUrl' => $verificationUrl,
            'qrCodeDataUri' => $qrCodeDataUri,
        ])->setPaper('a4');

        $skl->forceFill([
            'downloaded_at' => now(),
            'download_count' => ((int) $skl->download_count) + 1,
        ])->save();

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "SKL-{$student?->nisn}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    protected function getSkl(): Skl
    {
        $student = Filament::auth()->user();

        abort_unless($student, 403);

        return Skl::query()
            ->where('student_id', $student->getAuthIdentifier())
            ->firstOrFail();
    }

    protected function getAverageScore(): float
    {
        $student = Filament::auth()->user();

        if (! $student) {
            return 0.0;
        }

        // Pakai query avg() supaya ringan.
        return (float) (Grade::query()
            ->where('student_id', $student->getAuthIdentifier())
            ->avg('score') ?? 0);
    }
}
