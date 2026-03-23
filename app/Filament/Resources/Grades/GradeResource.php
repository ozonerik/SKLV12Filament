<?php

namespace App\Filament\Resources\Grades;

use App\Filament\Resources\Grades\Pages\CreateGrade;
use App\Filament\Resources\Grades\Pages\EditGrade;
use App\Filament\Resources\Grades\Pages\ListGrades;
use App\Models\Grade;
use App\Models\Major;
use App\Models\SchoolYear;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

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
                    ->default(fn(?Grade $record): ?int => $record?->student?->school_year_id)
                    ->afterStateUpdated(fn($state, callable $set) => $set('student_id', null)),
                Select::make('major_id')
                    ->label('Jurusan')
                    ->options(Major::query()->orderBy('konsentrasi_keahlian')->pluck('konsentrasi_keahlian', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated(false)
                    ->default(fn(?Grade $record): ?int => $record?->student?->major_id)
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
                Select::make('subject_id')
                    ->relationship('subject', 'kode')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('score')
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('student.schoolYear.name')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.major.kode_jurusan')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable(),
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
                TextColumn::make('subject.kode')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
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
                    DeleteBulkAction::make(),
                ]),
            ]);
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
        return 2;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGrades::route('/'),
            'create' => CreateGrade::route('/create'),
            'edit' => EditGrade::route('/{record}/edit'),
        ];
    }
}
