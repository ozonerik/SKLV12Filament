<?php

namespace App\Filament\Resources\Skls;

use App\Filament\Resources\Skls\Pages\CreateSkl;
use App\Filament\Resources\Skls\Pages\EditSkl;
use App\Filament\Resources\Skls\Pages\ListSkls;
use App\Models\Grade;
use App\Models\Major;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Skl;
use App\Models\Student;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use RuntimeException;
use ZipArchive;

class SklResource extends Resource
{
    protected static ?string $model = Skl::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'SKL';

    protected static ?string $pluralModelLabel = 'SKL';

    protected static ?string $navigationLabel = 'SKL';

    protected static ?string $recordTitleAttribute = 'letter_number';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_year_id')
                    ->label('Tahun Pelajaran')
                    ->options(SchoolYear::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated(false)
                    ->default(fn(?Skl $record): ?int => $record?->student?->school_year_id)
                    ->afterStateUpdated(fn($state, callable $set) => $set('student_id', null)),
                Select::make('major_id')
                    ->label('Jurusan')
                    ->options(Major::query()->orderBy('konsentrasi_keahlian')->pluck('konsentrasi_keahlian', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated(false)
                    ->default(fn(?Skl $record): ?int => $record?->student?->major_id)
                    ->afterStateUpdated(fn($state, callable $set) => $set('student_id', null)),
                Select::make('student_id')
                    ->label('Nama Siswa')
                    ->relationship(
                        'student',
                        'name',
                        fn(Builder $query, callable $get) => $query
                            ->when($get('school_year_id'), fn(Builder $studentQuery, $schoolYearId) => $studentQuery->where('school_year_id', $schoolYearId))
                            ->when($get('major_id'), fn(Builder $studentQuery, $majorId) => $studentQuery->where('major_id', $majorId))
                            ->orderBy('nis')
                    )
                    ->getOptionLabelFromRecordUsing(fn($record): string => trim(($record->nis ?: '-') . ' - ' . $record->name))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(['Lulus' => 'Lulus', 'Tidak Lulus' => 'Tidak lulus'])
                    ->required(),
                DatePicker::make('letter_date')
                    ->label('Tanggal Surat')
                    ->native(false)
                    ->locale('id')
                    ->required()
                    ->closeOnDateSelection()
                    ->displayFormat('d/m/Y'),
                TextInput::make('letter_number')
                    ->label('Nomor Surat')
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label('Tanggal Terbit')
                    ->native(false)
                    ->locale('id')
                    ->required()
                    ->displayFormat('d/m/Y H:i')
                    ->closeOnDateSelection()
                    ->seconds(false),
                Toggle::make('is_questionnaire_completed')
                    ->label('Kuesioner Selesai')
                    ->default(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->recordTitleAttribute('letter_number')
            ->persistColumnsInSession(false)
            ->columns([
                // Menampilkan Tahun Pelajaran dari relasi Student -> SchoolYear
                TextColumn::make('student.schoolYear.name')
                    ->label('Tahun Pelajaran')
                    ->sortable(),
                // Menampilkan Jurusan dari relasi Student -> Major
                TextColumn::make('student.major.konsentrasi_keahlian')
                    ->label('Jurusan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('student.nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.jenis_kelamin')
                    ->label('Jenis Kelamin'),
                TextColumn::make('letter_number')
                    ->label('Nomor Surat')
                    ->searchable(),
                TextColumn::make('verification_code')
                    ->label('Kode Verifikasi')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn(string $state): string => match ($state) {
                        'Lulus' => 'success',
                        'Tidak Lulus', 'Tidak lulus' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('letter_date')
                    ->label('Tanggal Surat')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Tanggal Terbit')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('student.average_grade')
                    ->label('Rata-rata Nilai')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            Student::query()
                                ->selectRaw('COALESCE(AVG(grades.score), 0)')
                                ->leftJoin('grades', 'grades.student_id', '=', 'students.id')
                                ->whereColumn('students.id', 'skls.student_id')
                                ->groupBy('students.id'),
                            $direction,
                        );
                    }),
                IconColumn::make('is_questionnaire_completed')
                    ->label('Kuesioner Selesai')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Lulus')
                    ->options([
                        'Lulus' => 'Lulus',
                        'Tidak Lulus' => 'Tidak Lulus',
                        'Tidak lulus' => 'Tidak lulus',
                    ]),
                SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->options(Major::query()->orderBy('konsentrasi_keahlian')->pluck('konsentrasi_keahlian', 'id')->all())
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('student', function (Builder $studentQuery) use ($data): void {
                            $studentQuery->where('major_id', $data['value']);
                        });
                    }),
                SelectFilter::make('school_year_id')
                    ->label('Tahun Pelajaran')
                    ->options(SchoolYear::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('student', function (Builder $studentQuery) use ($data): void {
                            $studentQuery->where('school_year_id', $data['value']);
                        });
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('download_selected_skl')
                        ->label('Download SKL')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            if ($records->isEmpty()) {
                                return null;
                            }

                            if ($records->count() === 1) {
                                /** @var Skl $skl */
                                $skl = $records->first();
                                [$pdfOutput, $filename] = self::buildSklPdf($skl);

                                return response()->streamDownload(
                                    fn() => print($pdfOutput),
                                    $filename,
                                    ['Content-Type' => 'application/pdf']
                                );
                            }

                            $zipFileName = 'SKL-selected-' . now()->format('Ymd-His') . '.zip';
                            $tempDirectory = storage_path('app/temp');
                            if (! is_dir($tempDirectory)) {
                                mkdir($tempDirectory, 0775, true);
                            }

                            $zipPath = $tempDirectory . DIRECTORY_SEPARATOR . $zipFileName;
                            $zip = new ZipArchive();

                            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                                throw new RuntimeException('Gagal membuat file ZIP untuk unduhan SKL.');
                            }

                            foreach ($records as $index => $skl) {
                                [$pdfOutput, $filename] = self::buildSklPdf($skl, $index + 1);
                                $zip->addFromString($filename, $pdfOutput);
                            }

                            $zip->close();

                            return response()->streamDownload(
                                function () use ($zipPath): void {
                                    $stream = fopen($zipPath, 'rb');
                                    if ($stream !== false) {
                                        fpassthru($stream);
                                        fclose($stream);
                                    }

                                    if (file_exists($zipPath)) {
                                        unlink($zipPath);
                                    }
                                },
                                $zipFileName,
                                ['Content-Type' => 'application/zip']
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected static function buildSklPdf(Skl $skl, ?int $fallbackIndex = null): array
    {
        $skl->loadMissing(['student.major', 'student.schoolYear.headmaster']);

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
            'Umum' => $grades->filter(fn(Grade $grade) => ($grade->subject?->category ?? null) === 'Umum')->values(),
            'Kejuruan' => $grades->filter(fn(Grade $grade) => ($grade->subject?->category ?? null) === 'Kejuruan')->values(),
            'Pilihan' => $grades->filter(fn(Grade $grade) => ($grade->subject?->category ?? null) === 'Pilihan')->values(),
            'Mulok' => $grades->filter(fn(Grade $grade) => ($grade->subject?->category ?? null) === 'Mulok')->values(),
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
        ])->setOption('isRemoteEnabled', true)
            ->setPaper([0, 0, 595.28, 935.43], 'portrait');

        $skl->forceFill([
            'downloaded_at' => now(),
        ])->save();

        $filenameSuffix = $student?->nisn ?: $verificationCode ?: (string) ($fallbackIndex ?? $skl->getKey());

        return [$pdf->output(), "SKL-{$filenameSuffix}.pdf"];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Main';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSkls::route('/'),
            'create' => CreateSkl::route('/create'),
            'edit' => EditSkl::route('/{record}/edit'),
        ];
    }
}
