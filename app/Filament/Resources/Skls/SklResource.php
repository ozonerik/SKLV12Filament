<?php

namespace App\Filament\Resources\Skls;

use App\Filament\Resources\Skls\Pages\CreateSkl;
use App\Filament\Resources\Skls\Pages\EditSkl;
use App\Filament\Resources\Skls\Pages\ListSkls;
use App\Models\Major;
use App\Models\SchoolYear;
use App\Models\Skl;
use BackedEnum;
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

class SklResource extends Resource
{
    protected static ?string $model = Skl::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
                    ->default(fn (?Skl $record): ?int => $record?->student?->school_year_id)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('student_id', null)),
                Select::make('major_id')
                    ->label('Jurusan')
                    ->options(Major::query()->orderBy('konsentrasi_keahlian')->pluck('konsentrasi_keahlian', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated(false)
                    ->default(fn (?Skl $record): ?int => $record?->student?->major_id)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('student_id', null)),
                Select::make('student_id')
                    ->label('Nama Siswa')
                    ->relationship(
                        'student',
                        'name',
                        fn (Builder $query, callable $get) => $query
                            ->when($get('school_year_id'), fn (Builder $studentQuery, $schoolYearId) => $studentQuery->where('school_year_id', $schoolYearId))
                            ->when($get('major_id'), fn (Builder $studentQuery, $majorId) => $studentQuery->where('major_id', $majorId))
                            ->orderBy('nis')
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => trim(($record->nis ?: '-') . ' - ' . $record->name))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(['Lulus' => 'Lulus', 'Tidak Lulus' => 'Tidak lulus'])
                    ->required(),
                DatePicker::make('letter_date')
                        ->native(false)
                        ->locale('id')
                    ->required()
                    ->displayFormat('d/m/Y'),
                TextInput::make('letter_number')
                    ->required(),
                DateTimePicker::make('published_at')
                        ->native(false)
                        ->locale('id')
                    ->required()
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false),
                Toggle::make('is_questionnaire_completed')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->recordTitleAttribute('letter_number')
            ->columns([
                TextColumn::make('student.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                // Menampilkan Jurusan dari relasi Student -> Major
                TextColumn::make('student.major.konsentrasi_keahlian')
                    ->label('Jurusan')
                    ->sortable()
                    ->searchable(),
                // Menampilkan Tahun Pelajaran dari relasi Student -> SchoolYear
                TextColumn::make('student.schoolYear.name')
                    ->label('Tahun Pelajaran')
                    ->sortable(),
                TextColumn::make('letter_number')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'Lulus' => 'success',
                        'Tidak Lulus', 'Tidak lulus' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('letter_date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                IconColumn::make('is_questionnaire_completed')
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

    public static function getPages(): array
    {
        return [
            'index' => ListSkls::route('/'),
            'create' => CreateSkl::route('/create'),
            'edit' => EditSkl::route('/{record}/edit'),
        ];
    }
}
